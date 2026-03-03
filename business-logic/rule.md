# Quy Tắc Coding - HotelManagement

## 1. Repository Pattern

### 1.1. Cấu trúc

- **IRepository (Interface)**: Đặt tại `app/Abstractions/Repositories/`
- **Repository (Implementation)**: Đặt tại `app/Repositories/`

### 1.2. Quy tắc đặt tên method

Đối với các chức năng CRUD cơ bản, sử dụng các tên method sau:

- **Lưu (tạo mới + cập nhật)**: `save(Model $model): bool`
- **Xóa**: `delete(Model $model): bool`
- **Lấy theo ID**: `findById(int $id): ?Model`
- **Lấy theo điều kiện khác**: `findBy{Field}(...): ?Model` hoặc `get{Scope}(...)` tùy ngữ cảnh

> **Lý do dùng `save()` thay cho cả `add()` lẫn `update()`:**
> Eloquent Active Record đã tự phân biệt insert / update dựa vào việc model có `id` hay chưa.
> Repository không cần biết bạn đang tạo mới hay cập nhật — nó chỉ làm một việc:
> **"Lưu trạng thái hiện tại của object này vào Database"**.

### 1.3. Kiểu dữ liệu truyền vào

**Quyết định: Repository nhận vào Model Object (Active Record), không nhận DTO**

**Lý do:**

- ✅ **Hướng đối tượng**: Action thao tác trực tiếp trên object, Repository chỉ lưu
- ✅ **Tái sử dụng cao**: `save()` dùng được cho cả Create lẫn Update
- ✅ **Repository gọn nhẹ**: Không biết field nào đang thay đổi — chỉ lưu trạng thái
- ✅ **Action kiểm soát business logic**: Mọi quyết định về dữ liệu nằm trong Action
- ✅ **Nhất quán với Eloquent**: Tận dụng tối đa Active Record của Laravel

**Data Objects (DTO) vẫn được dùng** nhưng chỉ để vận chuyển dữ liệu từ Request vào Action (validation layer) — **không truyền xuống Repository**.

**Ví dụ:**

```php
// Interface
interface RoomRepositoryInterface
{
    public function findById(int $id): ?Room;

    public function save(Room $room): bool;

    public function delete(Room $room): bool;
}

// Implementation
class EloquentRoomRepository implements RoomRepositoryInterface
{
    public function findById(int $id): ?Room
    {
        return Room::find($id);
    }

    public function save(Room $room): bool
    {
        // Eloquent tự phân biệt INSERT / UPDATE dựa vào $room->exists
        return $room->save();
    }

    public function delete(Room $room): bool
    {
        return $room->delete();
    }
}
```

**Action sử dụng Repository:**

```php
// Tạo mới
class CreateRoomAction
{
    public function __construct(private RoomRepositoryInterface $roomRepository) {}

    public function handle(RoomData $data): Room
    {
        $room = new Room();
        $room->room_number = $data->roomNumber;
        $room->type_id     = $data->typeId;
        $room->status      = $data->status;

        $this->roomRepository->save($room);
        return $room;
    }
}

// Cập nhật
class UpdateRoomAction
{
    public function __construct(private RoomRepositoryInterface $roomRepository) {}

    public function handle(int $id, RoomData $data): Room
    {
        $room = $this->roomRepository->findById($id);
        if (!$room) {
            throw new \Exception('Room not found');
        }

        $room->room_number = $data->roomNumber;
        $room->type_id     = $data->typeId;
        $room->status      = $data->status;

        $this->roomRepository->save($room); // cùng method, Eloquent tự UPDATE
        return $room;
    }
}
```

### 1.4. Đăng ký DI Container

Tất cả Repository phải được đăng ký trong Service Provider:

```php
// app/Providers/AppServiceProvider.php hoặc RepositoryServiceProvider.php
public function register(): void
{
    $this->app->bind(IRoomRepository::class, RoomRepository::class);
    $this->app->bind(IBookingRepository::class, BookingRepository::class);
    // ...
}
```

---

## 2. Action Pattern (Command/Query Separation)

### 2.1. Nguyên tắc

**KHÔNG xử lý logic trong Controller**. Controller chỉ làm nhiệm vụ:

- Nhận request
- Gọi Action
- Trả về response

### 2.2. Phân loại Action

#### 2.2.1. Command (Thay đổi dữ liệu)

Dùng cho: **Thêm, Sửa, Xóa**

- ✅ **SỬ DỤNG Repository**
- ✅ **Action chịu trách nhiệm set fields lên Model trước khi gọi `save()`**
- ❌ **KHÔNG truy vấn trực tiếp Model**
- ❌ **KHÔNG truyền DTO xuống Repository**

**Ví dụ:**

```php
// app/Actions/Room/CreateRoomAction.php
class CreateRoomAction
{
    public function __construct(
        private RoomRepositoryInterface $roomRepository
    ) {}

    public function handle(RoomData $data): Room
    {
        $room = new Room();
        $room->room_number = $data->roomNumber;
        $room->type_id     = $data->typeId;
        $room->status      = $data->status;

        $this->roomRepository->save($room);
        return $room;
    }
}

// app/Actions/Room/UpdateRoomAction.php
class UpdateRoomAction
{
    public function __construct(
        private RoomRepositoryInterface $roomRepository
    ) {}

    public function handle(int $id, RoomData $data): Room
    {
        $room = $this->roomRepository->findById($id);
        if (!$room) {
            throw new \Exception('Room not found');
        }

        $room->room_number = $data->roomNumber;
        $room->type_id     = $data->typeId;
        $room->status      = $data->status;

        $this->roomRepository->save($room);
        return $room;
    }
}

// app/Actions/Room/DeleteRoomAction.php
class DeleteRoomAction
{
    public function __construct(
        private RoomRepositoryInterface $roomRepository
    ) {}

    public function handle(int $id): void
    {
        $room = $this->roomRepository->findById($id);
        if (!$room) {
            throw new \Exception('Room not found');
        }

        $this->roomRepository->delete($room);
    }
}
```

#### 2.2.2. Query (Chỉ đọc dữ liệu)

Dùng cho: **Lấy danh sách, Tìm kiếm, Thống kê**

- ✅ **SỬ DỤNG Eloquent trực tiếp**
- ❌ **KHÔNG cần qua Repository**

**Ví dụ:**

```php
// app/Actions/Room/GetRoomListAction.php
class GetRoomListAction
{
    public function execute(array $filters = []): Collection
    {
        $query = Room::query()->with(['type', 'floor']);

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['type_id'])) {
            $query->where('type_id', $filters['type_id']);
        }

        return $query->get();
    }
}

// app/Actions/Room/GetAvailableRoomsAction.php
class GetAvailableRoomsAction
{
    public function execute(string $checkIn, string $checkOut): Collection
    {
        return Room::whereNotIn('id', function ($query) use ($checkIn, $checkOut) {
            $query->select('room_id')
                ->from('bookings')
                ->where('status', 'confirmed')
                ->where(function ($q) use ($checkIn, $checkOut) {
                    $q->whereBetween('check_in', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out', [$checkIn, $checkOut]);
                });
        })->get();
    }
}
```

### 2.3. Sử dụng trong Controller

```php
// app/Http/Controllers/RoomController.php
class RoomController extends Controller
{
    public function store(
        CreateRoomRequest $request,
        CreateRoomAction $action
    ) {
        $data = RoomData::from($request->validated());
        $room = $action->execute($data);

        return response()->json($room, 201);
    }

    public function update(
        int $id,
        UpdateRoomRequest $request,
        UpdateRoomAction $action
    ) {
        $data = RoomData::from($request->validated());
        $room = $action->execute($id, $data);

        return response()->json($room);
    }

    public function destroy(
        int $id,
        DeleteRoomAction $action
    ) {
        $action->execute($id);

        return response()->json(null, 204);
    }

    public function index(
        Request $request,
        GetRoomListAction $action
    ) {
        $rooms = $action->execute($request->all());

        return response()->json($rooms);
    }
}
```

---

## 3. Tóm tắt Workflow

### 3.1. Tạo chức năng mới (CRUD)

1. **Tạo Data Object** (nếu chưa có) - `app/Data/` — dùng để vận chuyển dữ liệu từ Request vào Action
2. **Tạo Repository Interface** - `app/Abstractions/Repositories/` — chỉ expose `findById`, `save`, `delete` và các finder cần thiết
3. **Tạo Repository Implementation** - `app/Repositories/` — mỗi method gọn nhẹ, chỉ delegate xuống Eloquent
4. **Đăng ký DI Container** - `app/Providers/AppServiceProvider.php`
5. **Tạo Action cho Command** - `app/Actions/{Module}/` — Action tự set fields lên Model rồi gọi `repository->save($model)`
6. **Tạo Action cho Query** - `app/Actions/{Module}/` — dùng Eloquent trực tiếp
7. **Tạo ViewModel** (nếu View/API cần chuẩn bị dữ liệu phức tạp) - `app/ViewModels/`
8. **Tạo Controller** - chỉ inject Action/ViewModel và trả về response

### 3.2. Checklist

- [ ] Data Object đã được tạo (nếu cần validation từ Request)?
- [ ] Repository Interface chỉ có `findById`, `save`, `delete` và các finder cần thiết?
- [ ] Repository Implementation **không** chứa logic set field — chỉ gọi `$model->save()`?
- [ ] Repository đã được đăng ký trong DI Container?
- [ ] Command Action tự `new Model()` / `findById()` → set fields → `repository->save($model)`?
- [ ] **KHÔNG** truyền DTO xuống Repository?
- [ ] Query Actions đã sử dụng Eloquent trực tiếp?
- [ ] ViewModel đã được tạo cho View/API cần chuẩn bị dữ liệu phức tạp?
- [ ] Controller KHÔNG chứa business logic và logic chuẩn bị dữ liệu?

---

## 4. Lưu ý quan trọng

### 4.1. Khi nào dùng Repository?

- ✅ Command: Create, Update, Delete
- ✅ Khi cần getById cho Command
- ❌ Query phức tạp (dùng Eloquent trực tiếp)
- ❌ Reporting (dùng Eloquent trực tiếp)

### 4.2. Lợi ích của pattern này

- **Separation of Concerns**: Mỗi layer có trách nhiệm riêng
- **Testability**: Dễ dàng mock Repository và Action
- **Maintainability**: Dễ bảo trì và mở rộng
- **CQRS principles**: Tách biệt Command và Query
- **Clean Architecture**: Tuân thủ nguyên tắc kiến trúc sạch

---

## 5. ViewModel Pattern

### 5.1. Khái niệm

**ViewModel** là class đóng gói logic xử lý và chuẩn bị dữ liệu cho View. Khi nhắc đến View, không chỉ là HTML/Blade mà còn bao gồm cả API trả về JSON/XML.

**Mục đích:** Tránh đẩy logic xử lý dữ liệu xuống Actions/Repositories, giúp code tổ chức tốt hơn, tái sử dụng và linh hoạt trước sự thay đổi nghiệp vụ.

### 5.2. Tại sao cần ViewModel?

#### Vấn đề khi KHÔNG dùng ViewModel:

```php
// Controller phình to với logic chuẩn bị dữ liệu
public function create()
{
    return view('customer.create', [
        'customerTypes' => CustomerType::all(),
        'countries' => Country::all(),
    ]);
}

public function edit($id)
{
    $customer = Customer::find($id);
    return view('customer.create', [
        'customer' => $customer,
        'customerTypes' => CustomerType::all(),
        'countries' => Country::all(),
    ]);
}

// Khi nghiệp vụ thay đổi (thêm tags, labels, filter theo quyền)
// Bạn phải sửa TẤT CẢ các method trên!
```

**Vấn đề:**

- ❌ Code lặp lại nhiều chỗ
- ❌ Khó bảo trì khi nghiệp vụ thay đổi
- ❌ Không biết View có những biến/method nào
- ❌ Controller phình to với logic chuẩn bị dữ liệu

#### Giải pháp: Sử dụng ViewModel

ViewModel đóng gói tất cả logic có thể tái sử dụng, với chức năng duy nhất: **Chuẩn bị dữ liệu chính xác cho View**.

### 5.3. Đặc điểm quan trọng của ViewModel

- ✅ **Dependency Injection**: Linh hoạt, dễ test
- ✅ **Chuẩn hoá methods**: View biết chính xác method nào có sẵn
- ✅ **Đóng gói**: Tái sử dụng triệt để
- ✅ **Single Responsibility**: Chỉ lo chuẩn bị dữ liệu cho View

### 5.4. Cấu trúc thư mục

```
app/
  ViewModels/
    CustomerViewModel.php
    BookingViewModel.php
    RoomViewModel.php
```

### 5.5. Ví dụ cơ bản: Customer Form

#### 5.5.1. ViewModel Class

```php
// app/ViewModels/CustomerViewModel.php
namespace App\ViewModels;

use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Country;
use Illuminate\Support\Collection;

class CustomerViewModel
{
    private ?Customer $customer;

    public function __construct(Customer $customer = null)
    {
        $this->customer = $customer;
    }

    /**
     * Trả về customer (mới hoặc existing)
     */
    public function customer(): Customer
    {
        return $this->customer ?? new Customer();
    }

    /**
     * Danh sách loại khách hàng
     */
    public function customerTypes(): Collection
    {
        return CustomerType::select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    /**
     * Danh sách quốc gia
     */
    public function countries(): Collection
    {
        return Country::select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    /**
     * Danh sách loại giấy tờ
     */
    public function idTypes(): array
    {
        return [
            ['value' => 'CMND', 'label' => 'Chứng minh nhân dân'],
            ['value' => 'CCCD', 'label' => 'Căn cước công dân'],
            ['value' => 'Passport', 'label' => 'Hộ chiếu'],
        ];
    }
}
```

#### 5.5.2. Controller sử dụng ViewModel

```php
// app/Http/Controllers/CustomerController.php
class CustomerController extends Controller
{
    // Form tạo mới
    public function create()
    {
        $viewModel = new CustomerViewModel();

        // Blade View
        return view('customer.form', compact('viewModel'));

        // Hoặc API JSON
        // return response()->json([
        //     'customer' => $viewModel->customer(),
        //     'customerTypes' => $viewModel->customerTypes(),
        //     'countries' => $viewModel->countries(),
        //     'idTypes' => $viewModel->idTypes(),
        // ]);
    }

    // Form cập nhật
    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $viewModel = new CustomerViewModel($customer);

        // Blade View
        return view('customer.form', compact('viewModel'));

        // Hoặc API JSON
        // return response()->json([
        //     'customer' => $viewModel->customer(),
        //     'customerTypes' => $viewModel->customerTypes(),
        //     'countries' => $viewModel->countries(),
        //     'idTypes' => $viewModel->idTypes(),
        // ]);
    }
}
```

#### 5.5.3. Sử dụng trong Blade View

```blade
{{-- resources/views/customer/form.blade.php --}}
<h1>{{ $viewModel->customer()->exists ? 'Cập nhật' : 'Tạo mới' }} khách hàng</h1>

<form>
    <input type="text" name="name" value="{{ $viewModel->customer()->name }}">

    <select name="customer_type_id">
        @foreach ($viewModel->customerTypes() as $type)
            <option value="{{ $type->id }}"
                {{ $viewModel->customer()->customer_type_id == $type->id ? 'selected' : '' }}>
                {{ $type->name }}
            </option>
        @endforeach
    </select>

    <select name="country_id">
        @foreach ($viewModel->countries() as $country)
            <option value="{{ $country->id }}"
                {{ $viewModel->customer()->country_id == $country->id ? 'selected' : '' }}>
                {{ $country->name }}
            </option>
        @endforeach
    </select>

    <select name="id_type">
        @foreach ($viewModel->idTypes() as $idType)
            <option value="{{ $idType['value'] }}"
                {{ $viewModel->customer()->id_type == $idType['value'] ? 'selected' : '' }}>
                {{ $idType['label'] }}
            </option>
        @endforeach
    </select>
</form>
```

#### 5.5.4. Trả về JSON cho API

```php
// app/Http/Controllers/Api/CustomerController.php
class CustomerController extends Controller
{
    public function create()
    {
        $viewModel = new CustomerViewModel();

        return response()->json([
            'customer' => $viewModel->customer(),
            'masterData' => [
                'customerTypes' => $viewModel->customerTypes(),
                'countries' => $viewModel->countries(),
                'idTypes' => $viewModel->idTypes(),
            ]
        ]);
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        $viewModel = new CustomerViewModel($customer);

        return response()->json([
            'customer' => $viewModel->customer(),
            'masterData' => [
                'customerTypes' => $viewModel->customerTypes(),
                'countries' => $viewModel->countries(),
                'idTypes' => $viewModel->idTypes(),
            ]
        ]);
    }
}
```

### 5.6. Ví dụ nâng cao: Booking với logic phức tạp

```php
// app/ViewModels/BookingViewModel.php
namespace App\ViewModels;

use App\Models\Booking;
use App\Models\Room;
use App\Models\Service;
use App\Models\PaymentMethod;
use Illuminate\Support\Collection;

class BookingViewModel
{
    private ?Booking $booking;
    private ?string $checkIn;
    private ?string $checkOut;

    public function __construct(
        Booking $booking = null,
        string $checkIn = null,
        string $checkOut = null
    ) {
        $this->booking = $booking;
        $this->checkIn = $checkIn ?? $booking?->check_in;
        $this->checkOut = $checkOut ?? $booking?->check_out;
    }

    public function booking(): Booking
    {
        return $this->booking ?? new Booking();
    }

    /**
     * Phòng trống trong khoảng thời gian
     */
    public function availableRooms(): Collection
    {
        $query = Room::query();

        // Nếu đang edit, bao gồm phòng hiện tại
        if ($this->booking?->exists) {
            $query->where(function($q) {
                $q->where('id', $this->booking->room_id)
                  ->orWhereNotIn('id', $this->bookedRoomIds());
            });
        } else {
            $query->whereNotIn('id', $this->bookedRoomIds());
        }

        return $query->with('type')
            ->get()
            ->map(fn($room) => [
                'id' => $room->id,
                'room_number' => $room->room_number,
                'type' => $room->type->name,
                'price' => $room->type->price,
            ]);
    }

    /**
     * Danh sách dịch vụ
     */
    public function services(): Collection
    {
        return Service::where('is_active', true)
            ->select('id', 'name', 'price')
            ->get();
    }

    /**
     * Phương thức thanh toán
     */
    public function paymentMethods(): Collection
    {
        return PaymentMethod::where('is_active', true)
            ->select('id', 'name')
            ->get();
    }

    /**
     * Lấy danh sách ID phòng đã được đặt
     */
    private function bookedRoomIds(): array
    {
        return Booking::where('status', '!=', 'cancelled')
            ->when($this->booking?->exists, function($q) {
                $q->where('id', '!=', $this->booking->id);
            })
            ->where(function ($q) {
                $q->whereBetween('check_in', [$this->checkIn, $this->checkOut])
                  ->orWhereBetween('check_out', [$this->checkIn, $this->checkOut])
                  ->orWhere(function($subQ) {
                      $subQ->where('check_in', '<=', $this->checkIn)
                           ->where('check_out', '>=', $this->checkOut);
                  });
            })
            ->pluck('room_id')
            ->toArray();
    }
}
```

**Controller:**

```php
class BookingController extends Controller
{
    public function create(Request $request)
    {
        $viewModel = new BookingViewModel(
            checkIn: $request->check_in,
            checkOut: $request->check_out
        );

        return response()->json([
            'booking' => $viewModel->booking(),
            'masterData' => [
                'availableRooms' => $viewModel->availableRooms(),
                'services' => $viewModel->services(),
                'paymentMethods' => $viewModel->paymentMethods(),
            ]
        ]);
    }

    public function edit($id)
    {
        $booking = Booking::with(['room', 'services'])->findOrFail($id);
        $viewModel = new BookingViewModel($booking);

        return response()->json([
            'booking' => $viewModel->booking(),
            'masterData' => [
                'availableRooms' => $viewModel->availableRooms(),
                'services' => $viewModel->services(),
                'paymentMethods' => $viewModel->paymentMethods(),
            ]
        ]);
    }
}
```

### 5.7. Lợi ích khi thay đổi nghiệp vụ

**Khi cần thêm logic mới** (ví dụ: filter theo quyền user, thêm tags):

```php
class CustomerViewModel
{
    private ?Customer $customer;
    private ?User $user; // Thêm user

    public function __construct(Customer $customer = null, User $user = null)
    {
        $this->customer = $customer;
        $this->user = $user;
    }

    // Method cũ không đổi
    public function customer(): Customer
    {
        return $this->customer ?? new Customer();
    }

    // Thêm method mới
    public function tags(): Collection
    {
        return Tag::all();
    }

    // Filter theo quyền
    public function customerTypes(): Collection
    {
        $query = CustomerType::query();

        if ($this->user && !$this->user->isAdmin()) {
            $query->where('is_public', true);
        }

        return $query->select('id', 'name')->get();
    }
}
```

**Chỉ cần sửa ViewModel**, tất cả nơi sử dụng đều được cập nhật!

### 5.8. Best Practices

#### ✅ NÊN:

- Sử dụng Dependency Injection trong constructor
- Đặt tên method rõ ràng, trả về kiểu dữ liệu cụ thể: `customerTypes(): Collection`
- Cache dữ liệu ít thay đổi (countries, categories)
- Private methods cho logic phức tạp (`bookedRoomIds()`)
- Một ViewModel có thể dùng cho cả create và edit

#### ❌ KHÔNG NÊN:

- Đưa logic business (tạo, sửa, xóa) vào ViewModel
- Load toàn bộ Model với đầy đủ relations
- Truy vấn không cần thiết
- Static methods (trừ factory methods nếu cần)

### 5.9. Khi nào dùng ViewModel?

| Tình huống                                | Dùng ViewModel?                     |
| ----------------------------------------- | ----------------------------------- |
| Form create/edit cần dropdown/master data | ✅ NÊN                              |
| API trả về entity + master data           | ✅ NÊN                              |
| View cần logic chuẩn bị dữ liệu phức tạp  | ✅ NÊN                              |
| API chỉ trả về entity đơn giản            | ❌ KHÔNG (dùng Data Object)         |
| API list đơn giản                         | ❌ KHÔNG (dùng Collection)          |
| Logic business (create, update, delete)   | ❌ KHÔNG (dùng Action + Repository) |

### 5.10. Tóm tắt

**ViewModel = Class chuẩn bị dữ liệu cho View/API**

- Đóng gói logic xử lý dữ liệu cho View
- Tái sử dụng cho create và edit
- Dễ mở rộng khi nghiệp vụ thay đổi
- View/API biết chính xác method nào có sẵn
- KHÔNG chứa logic business (dùng Action cho việc đó)
