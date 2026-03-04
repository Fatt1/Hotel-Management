# Room Type Management - Architecture Implementation

## Cấu trúc đã tạo

### 1. **Repository Pattern** 
Tuân theo design pattern Repository để tách riêng dữ liệu access layer

#### Interface
📁 `app/Abstractions/Repositories/RoomTypeRepository.php`
```php
- findById(int $id): ?RoomType
- save(RoomType $roomType): bool
- delete(RoomType $roomType): bool
- all(): array
- existsByCode(string $code, ?int $excludeId = null): bool
```

#### Implementation
📁 `app/Repositories/EloquentRoomTypeRepository.php`
- Delegates tất cả operations tới Eloquent Model
- Không chứa business logic
- Chỉ là adapter giữa Domain layer và Database layer

---

### 2. **Action Pattern (Command Query Separation)**

#### Command Actions (Thay đổi dữ liệu)

**📁 `app/Actions/RoomTypes/CreateRoomTypeAction.php`**
- Nhận `RoomTypeData` (DTO)
- Tạo Model mới
- Set tất cả fields trực tiếp từ DTO
- Gọi `repository->save($model)`
- Trả về `RoomType` Model sau khi lưu
- Flow: Controller → DTO → Action → Repository → Database

**📁 `app/Actions/RoomTypes/UpdateRoomTypeAction.php`**
- Nhận `id` và `RoomTypeData` (DTO)
- Lấy existing Model via `repository->findById($id)`
- Update các fields
- Gọi `repository->save($model)`
- Trả về updated `RoomType`

**📁 `app/Actions/RoomTypes/DeleteRoomTypeAction.php`**
- **Business Rule Implementation**: Kiểm tra xem có phòng thuộc loại này không
- Nếu có phòng → Throw Exception: "Không thể xóa loại phòng này vì đang có X phòng thuộc loại này"
- Nếu không có → Gọi `repository->delete($roomType)`
- Tuân theo quy tắc: `Không được xóa Room Type nếu có phòng thuộc loại đó`

#### Query Actions (Chỉ đọc dữ liệu)

**📁 `app/Actions/RoomTypes/GetRoomTypeListAction.php`**
- Không sử dụng Repository
- Trực tiếp queries Eloquent Model
- Hỗ trợ filter:
  - Tìm kiếm theo tên/code
  - Filter theo giá
  - Sắp xếp (sort_by, sort_order)
- Methods:
  - `execute(array $filters): Collection` - Trả về Collection
  - `executeWithRoomCount(array $filters): Collection` - Thêm info số phòng

---

### 3. **Data Transfer Objects (DTO)**

📁 `app/Data/RoomTypeData.php`
```php
class RoomTypeData extends Data
{
    public string $name;
    public string $code;
    public int $adult_quantity;
    public int $child_quantity;
    public int $single_bed_quantity;
    public int $double_bed_quantity;
    public float $width;
    public float $height;
    public float $hourly_price;
    public float $daily_price;
    public ?string $description = null;
}
```

**Công dụng:**
- Validate dữ liệu từ Form Request
- Vận chuyển dữ liệu từ Controller → Action
- Type-safe, tránh type mismatch

---

### 4. **ViewModel Pattern**

📁 `app/ViewModels/RoomTypeViewModel.php`

**Mục đích:** Chuẩn bị dữ liệu cho View/API

**Methods:**
- `roomType(): RoomType` - Trả về model (mới hoặc existing)
- `roomTypeOptions(): Collection` - List for dropdown
- `hasRooms(): bool` - Kiểm tra có phòng không
- `totalRooms(): int` - Tổng số phòng
- `availableRooms(): int` - Số phòng trống
- `dimensions(): array` - Info kích thước (width, height, area)
- `pricing(): array` - Info giá (hourly, daily)
- `capacity(): array` - Info sức chứa (adults, children, beds, total_guests)
- `amenities(): Collection` - Tiện ích
- `equipment(): Collection` - Thiết bị
- `images(): Collection` - Hình ảnh
- `imageUrls(): array` - URLs hình ảnh

**Lợi ích:**
- View biết chính xác method nào có sẵn
- Dễ mở rộng khi business logic thay đổi
- Tái sử dụng cho create/edit
- Một nơi quản lý tất cả logic chuẩn bị dữ liệu

---

### 5. **Dependency Injection Container**

📁 `app/Providers/AppServiceProvider.php`

```php
$this->app->bind(RoomTypeRepository::class, EloquentRoomTypeRepository::class);
```

**Lợi ích:**
- Dễ dàng swap implementation
- Dễ test (mock Repository)
- Loosely coupled

---

### 6. **Controller (Updated)**

📁 `app/Http/Controllers/Admin/RoomTypeAdminController.php`

**Workflow:**
1. Inject Action vào method via Dependency Injection
2. Tạo `RoomTypeData` từ `Request` validated data
3. Gọi `Action->execute(data)`
4. Handle exception
5. Trả về response với redirect/flash message

**Ví dụ:**
```php
public function store(RoomTypeRequest $request, CreateRoomTypeAction $action)
{
    try {
        $data = RoomTypeData::from($request->validated());
        $action->execute($data);
        return redirect()->with('success', 'Loại phòng đã được tạo thành công');
    } catch (\Exception $e) {
        return redirect()->with('error', $e->getMessage());
    }
}
```

---

## Quy tắc tuân theo

### ✅ Repository Pattern
- Interface tại `app/Abstractions/Repositories/`
- Implementation tại `app/Repositories/`
- Methods: `findById()`, `save()`, `delete()` và finder methods
- **Repository nhận Model, không nhận DTO**

### ✅ Action Pattern
- **Command**: Tự set fields → `repository->save($model)`
- **Query**: Dùng Eloquent trực tiếp
- **KHÔNG truyền DTO xuống Repository**
- Xử lý Exception trong Action, trả về để Controller handle

### ✅ ViewModel Pattern
- Chuẩn bị dữ liệu cho View
- Đóng gói logic phức tạp
- Tái sử dụng cho multiple actions
- KHÔNG chứa business logic (dùng Action cho việc đó)

### ✅ Business Logic
- DeleteRoomTypeAction: Kiểm tra Room.count() trước khi xóa
- Exception message: "Không thể xóa loại phòng này vì đang có X phòng thuộc loại này"
- Tuân theo file `business-logic/business.md`

---

## Luồng dữ liệu

### Create/Update Flow
```
Blade Form
    ↓ (POST/PUT request)
RoomTypeRequest (Validation)
    ↓
RoomTypeData::from($validated) (DTO)
    ↓
CreateRoomTypeAction/UpdateRoomTypeAction
    ↓ (Tạo/Update Model + set fields)
RoomTypeRepository::save($model)
    ↓
Eloquent Model::save() (INSERT/UPDATE)
    ↓
Database
    ↓
Redirect with Flash Message
```

### Read Flow
```
View/API Request
    ↓
Controller inject GetRoomTypeListAction
    ↓
GetRoomTypeListAction::execute($filters)
    ↓
RoomType::query()->where(...)->get() (Direct Eloquent)
    ↓
RoomTypeViewModel::executeWithRoomCount()
    ↓
Formatted data + room counts
    ↓
Return Collection/JSON
```

### View Preparation
```
Controller::create()
    ↓
new RoomTypeViewModel()
    ↓
view('admin.room-type.create', compact('viewModel'))
    ↓
Blade: $viewModel->roomTypeOptions(), 
       $viewModel->capacity(), etc.
```

---

## Files Checklist

- ✅ `app/Abstractions/Repositories/RoomTypeRepository.php` - Interface
- ✅ `app/Repositories/EloquentRoomTypeRepository.php` - Implementation
- ✅ `app/Actions/RoomTypes/CreateRoomTypeAction.php` - Command
- ✅ `app/Actions/RoomTypes/UpdateRoomTypeAction.php` - Command
- ✅ `app/Actions/RoomTypes/DeleteRoomTypeAction.php` - Command (with Business Logic)
- ✅ `app/Actions/RoomTypes/GetRoomTypeListAction.php` - Query
- ✅ `app/Data/RoomTypeData.php` - DTO
- ✅ `app/ViewModels/RoomTypeViewModel.php` - View preparation
- ✅ `app/Providers/AppServiceProvider.php` - DI binding
- ✅ `app/Http/Controllers/Admin/RoomTypeAdminController.php` - Updated to use Actions

---

## Tiếp theo

1. **Update Views** để sử dụng `$viewModel`:
   - `resources/views/admin/room-type/create.blade.php` - Remove hardcoded values, sử dụng ViewModel
   - `resources/views/admin/room-type/edit.blade.php` - Pass ViewModel
   - `resources/views/admin/room-type/show.blade.php` - Thêm dữ liệu động từ ViewModel

2. **Add API endpoints** (optional):
   - `api/room-types` - GET list, POST create, PUT update, DELETE destroy
   - Reuse Actions để tránh duplicate logic

3. **Add Testing** (optional):
   - Mock RoomTypeRepository
   - Test Actions với edge cases
   - Test Business Logic (delete with rooms)

---

## Refs
- 📄 `business-logic/business.md` - Business rules
- 📄 `business-logic/rule.md` - Coding guidelines
