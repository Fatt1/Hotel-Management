# Business Logic - Hotel Management System

## 1. Quản lý Role (Vai trò)

### 1.1. Ràng buộc xóa Role

**Rule:** Không được xóa Role nếu đang được sử dụng

**Chi tiết:**

- Role đang được gán cho User/Staff thì **KHÔNG được phép xóa**
- Kiểm tra trước khi xóa: `User::where('role_id', $roleId)->exists()`
- Nếu có User sử dụng → Trả về lỗi: "Không thể xóa vai trò này vì đang có người dùng sử dụng"

**Implementation:**

```php
// app/Actions/Role/DeleteRoleAction.php
public function execute(int $roleId): bool
{
    // Kiểm tra role đang được sử dụng
    if (User::where('role_id', $roleId)->exists()) {
        throw new BusinessException(
            'Không thể xóa vai trò này vì đang có người dùng sử dụng'
        );
    }

    return $this->roleRepository->delete($roleId);
}
```

---

## 2. Quản lý Staff (Nhân viên)

### 2.1. Ràng buộc xóa Staff

**Rule:** Không được xóa Staff nếu đã thực hiện nghiệp vụ

**Chi tiết:**

- Staff đã tạo Booking thì **KHÔNG được phép xóa**
- Kiểm tra trước khi xóa: `Booking::where('created_by_staff_id', $staffId)->exists()`
- Nếu có Booking → Trả về lỗi: "Không thể xóa nhân viên này vì đã thực hiện các giao dịch đặt phòng"
- Có thể chuyển sang trạng thái "Inactive" thay vì xóa

**Implementation:**

```php
// app/Actions/Staff/DeleteStaffAction.php
public function execute(int $staffId): bool
{
    // Kiểm tra staff đã tạo booking
    if (Booking::where('created_by_staff_id', $staffId)->exists()) {
        throw new BusinessException(
            'Không thể xóa nhân viên này vì đã thực hiện các giao dịch đặt phòng'
        );
    }

    return $this->staffRepository->delete($staffId);
}
```

**Alternative:** Soft delete hoặc deactivate thay vì xóa

```php
// Chuyển sang inactive
$staff->update(['is_active' => false]);
```

---

## 3. Quản lý Room Type (Loại phòng)

### 3.1. Ràng buộc xóa Room Type

**Rule:** Không được xóa Room Type nếu có phòng thuộc loại đó

**Chi tiết:**

- Room Type có Room thuộc loại đó thì **KHÔNG được phép xóa**
- Kiểm tra trước khi xóa: `Room::where('room_type_id', $roomTypeId)->exists()`
- Nếu có Room → Trả về lỗi: "Không thể xóa loại phòng này vì đang có phòng thuộc loại này"
- Phải xóa hoặc chuyển Room sang loại khác trước

**Implementation:**

```php
// app/Actions/RoomType/DeleteRoomTypeAction.php
public function execute(int $roomTypeId): bool
{
    // Kiểm tra có phòng thuộc loại này
    $roomCount = Room::where('room_type_id', $roomTypeId)->count();

    if ($roomCount > 0) {
        throw new BusinessException(
            "Không thể xóa loại phòng này vì đang có {$roomCount} phòng thuộc loại này"
        );
    }

    return $this->roomTypeRepository->delete($roomTypeId);
}
```

---

## 4. Xác thực Client (Khách hàng)

### 4.1. Đăng nhập với OTP qua Email

**Rule:** Client đăng nhập bằng Email + OTP

**Flow:**

1. Client nhập Email
2. Hệ thống gửi mã OTP (6 số) qua Email
3. OTP có thời hạn: **5 phút**
4. Client nhập OTP để xác thực
5. Nếu đúng → Tạo session/token đăng nhập

**Implementation:**

```php
// app/Actions/Auth/SendOTPAction.php
public function execute(string $email): void
{
    // Tạo mã OTP 6 số
    $otp = rand(100000, 999999);

    // Lưu OTP vào cache/database với TTL 5 phút
    Cache::put("otp:{$email}", $otp, now()->addMinutes(5));

    // Gửi email
    Mail::to($email)->send(new OTPMail($otp));
}

// app/Actions/Auth/VerifyOTPAction.php
public function execute(string $email, string $otp): bool
{
    $cachedOTP = Cache::get("otp:{$email}");

    if (!$cachedOTP || $cachedOTP !== $otp) {
        throw new BusinessException('Mã OTP không chính xác hoặc đã hết hạn');
    }

    // Xóa OTP đã sử dụng
    Cache::forget("otp:{$email}");

    return true;
}
```

**Email Template:**

```
Mã OTP của bạn là: 123456
Mã này có hiệu lực trong 5 phút.
```

### 4.2. Quản lý Email và Thông tin cá nhân

**Rule:** Email là duy nhất và liên kết với hồ sơ khách hàng

**Chi tiết:**

- Email là **unique** trong hệ thống
- Khi Client điền thông tin cá nhân:
    - Nếu Email **ĐÃ TỒN TẠI** → Auto-fill thông tin từ database
    - Nếu Email **CHƯA TỒN TẠI** → Cho phép điền mới
- **Cập nhật thông tin:** Chỉ được phép khi **ĐÃ ĐĂNG NHẬP** (xác thực OTP)

**Flow điền thông tin:**

```
1. Client nhập Email
   ↓
2. Kiểm tra Email trong DB
   ↓
   ├─ TỒN TẠI → Auto-fill: Name, Phone, Address, etc.
   │            → Khóa form, yêu cầu đăng nhập để sửa
   │
   └─ CHƯA TỒN TẠI → Form trống, cho phép điền mới
```

**Implementation:**

```php
// app/Actions/Customer/CheckEmailAction.php
public function execute(string $email): ?Customer
{
    return Customer::where('email', $email)->first();
}

// API Endpoint
// GET /api/customers/check-email?email=abc@example.com
public function checkEmail(Request $request)
{
    $customer = Customer::where('email', $request->email)->first();

    if ($customer) {
        return response()->json([
            'exists' => true,
            'customer' => [
                'name' => $customer->name,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'id_number' => $customer->id_number,
            ],
            'message' => 'Email này đã có trong hệ thống. Vui lòng đăng nhập để cập nhật thông tin.'
        ]);
    }

    return response()->json([
        'exists' => false,
        'message' => 'Vui lòng điền thông tin của bạn.'
    ]);
}

// app/Actions/Customer/UpdateCustomerInfoAction.php
public function execute(int $customerId, CustomerData $data, bool $isAuthenticated): Customer
{
    if (!$isAuthenticated) {
        throw new BusinessException('Bạn cần đăng nhập để cập nhật thông tin');
    }

    return $this->customerRepository->update($customerId, $data);
}
```

---

## 5. Booking (Đặt phòng)

### 5.1. Gửi hóa đơn sau khi đặt phòng thành công

**Rule:** Tự động gửi Invoice qua Email sau khi booking thành công

**Chi tiết:**

- Khi booking online **thành công** → Gửi hóa đơn PDF qua Email
- Nội dung Email:
    - Thông tin đặt phòng: Room, Check-in, Check-out
    - Thông tin khách hàng
    - Tổng tiền, phương thức thanh toán
    - Booking code (để tra cứu)
    - QR code (optional)

**Implementation:**

```php
// app/Actions/Booking/CreateOnlineBookingAction.php
public function execute(BookingData $data): Booking
{
    DB::beginTransaction();
    try {
        // Tạo booking
        $booking = $this->bookingRepository->add($data);

        // Gửi invoice qua email
        $this->sendInvoiceEmail($booking);

        DB::commit();
        return $booking;
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}

private function sendInvoiceEmail(Booking $booking): void
{
    $pdf = PDF::loadView('invoices.booking', compact('booking'));

    Mail::to($booking->customer->email)
        ->send(new BookingInvoiceMail($booking, $pdf));
}
```

**Email Invoice Template:**

```
Cảm ơn bạn đã đặt phòng tại [Hotel Name]!

Mã đặt phòng: BK2026001
Phòng: 101 - Deluxe Room
Check-in: 25/02/2026
Check-out: 27/02/2026
Tổng tiền: 2,000,000 VNĐ

Xem chi tiết trong file đính kèm.
```

### 5.2. Logic tìm kiếm và đặt phòng

**Flow tổng quát:**

```
1. Client chọn ngày Check-in & Check-out
   ↓
2. Tìm kiếm phòng trống theo ngày
   ↓
3. Hiển thị danh sách Room Types với số lượng phòng còn trống
   ↓
4. Client chọn loại phòng → Xem chi tiết
   ↓
5. Client chọn số lượng khách
   ↓
6. Hệ thống kiểm tra capacity
   ├─ ĐỦ → Cho phép đặt
   └─ KHÔNG ĐỦ → Gợi ý đặt nhiều phòng
   ↓
7. Đặt phòng
```

#### 5.2.1. Tìm kiếm phòng theo ngày

**Rule:** Hiển thị đúng số lượng phòng trống trong khoảng thời gian

**Logic:**

- Tìm các phòng **KHÔNG BỊ ĐẶT** trong khoảng `[check_in, check_out]`
- Phòng bị đặt khi:
    ```sql
    booking.status != 'cancelled' AND (
        (booking.check_in >= input_check_in AND booking.check_in < input_check_out) OR
        (booking.check_out > input_check_in AND booking.check_out <= input_check_out) OR
        (booking.check_in <= input_check_in AND booking.check_out >= input_check_out)
    )
    ```

**Implementation:**

```php
// app/Actions/Room/SearchAvailableRoomsAction.php
public function execute(string $checkIn, string $checkOut): Collection
{
    // Lấy danh sách room_id đã được đặt
    $bookedRoomIds = Booking::where('status', '!=', 'cancelled')
        ->where(function($query) use ($checkIn, $checkOut) {
            $query->whereBetween('check_in', [$checkIn, $checkOut])
                  ->orWhereBetween('check_out', [$checkIn, $checkOut])
                  ->orWhere(function($q) use ($checkIn, $checkOut) {
                      $q->where('check_in', '<=', $checkIn)
                        ->where('check_out', '>=', $checkOut);
                  });
        })
        ->pluck('room_id')
        ->toArray();

    // Lấy phòng trống, group by room_type
    return RoomType::withCount(['rooms' => function($query) use ($bookedRoomIds) {
        $query->whereNotIn('id', $bookedRoomIds)
              ->where('status', 'available');
    }])
    ->having('rooms_count', '>', 0)
    ->get()
    ->map(function($type) {
        return [
            'id' => $type->id,
            'name' => $type->name,
            'price' => $type->price,
            'max_guests' => $type->max_guests,
            'available_rooms' => $type->rooms_count,
            'description' => $type->description,
            'images' => $type->images,
        ];
    });
}
```

#### 5.2.2. Lọc theo số lượng khách

**Rule:** Kiểm tra capacity và gợi ý nếu không đủ chỗ

**Logic:**

- Mỗi `RoomType` có `max_guests` (số khách tối đa)
- Client chọn số lượng khách: `guest_count`
- Kiểm tra:
    - Nếu `guest_count <= max_guests` → Cho phép đặt 1 phòng
    - Nếu `guest_count > max_guests` → Gợi ý đặt nhiều phòng

**Implementation:**

```php
// app/Actions/Room/CheckRoomCapacityAction.php
public function execute(int $roomTypeId, int $guestCount, string $checkIn, string $checkOut): array
{
    $roomType = RoomType::findOrFail($roomTypeId);
    $availableRoomsCount = $this->getAvailableRoomsCount($roomTypeId, $checkIn, $checkOut);

    // Trường hợp 1: 1 phòng đủ chỗ
    if ($guestCount <= $roomType->max_guests) {
        return [
            'can_book' => true,
            'rooms_needed' => 1,
            'message' => 'Bạn có thể đặt 1 phòng cho số lượng khách này.'
        ];
    }

    // Trường hợp 2: Cần nhiều phòng
    $roomsNeeded = ceil($guestCount / $roomType->max_guests);

    if ($roomsNeeded > $availableRoomsCount) {
        return [
            'can_book' => false,
            'rooms_needed' => $roomsNeeded,
            'available_rooms' => $availableRoomsCount,
            'message' => "Không đủ phòng trống. Bạn cần {$roomsNeeded} phòng nhưng chỉ còn {$availableRoomsCount} phòng trống.",
            'suggestions' => $this->getSuggestions($guestCount, $checkIn, $checkOut)
        ];
    }

    return [
        'can_book' => true,
        'rooms_needed' => $roomsNeeded,
        'message' => "Gợi ý: Bạn nên đặt {$roomsNeeded} phòng {$roomType->name} để đủ chỗ cho {$guestCount} khách."
    ];
}

private function getSuggestions(int $guestCount, string $checkIn, string $checkOut): array
{
    // Tìm các combination phòng phù hợp
    $roomTypes = RoomType::all();
    $suggestions = [];

    // Ví dụ: 8 khách
    // Suggestion 1: 2 phòng Deluxe (4 khách/phòng)
    // Suggestion 2: 1 phòng Suite (6 khách) + 1 phòng Standard (2 khách)

    foreach ($roomTypes as $type) {
        $needed = ceil($guestCount / $type->max_guests);
        $available = $this->getAvailableRoomsCount($type->id, $checkIn, $checkOut);

        if ($needed <= $available) {
            $suggestions[] = [
                'room_type' => $type->name,
                'quantity' => $needed,
                'total_capacity' => $needed * $type->max_guests,
                'total_price' => $needed * $type->price,
            ];
        }
    }

    // Combination suggestions (optional)
    // Ví dụ: 1 Suite + 1 Standard

    return $suggestions;
}
```

**Response Example:**

```json
{
    "can_book": false,
    "rooms_needed": 3,
    "available_rooms": 2,
    "message": "Không đủ phòng trống. Bạn cần 3 phòng nhưng chỉ còn 2 phòng trống.",
    "suggestions": [
        {
            "room_type": "Suite Room",
            "quantity": 2,
            "total_capacity": 12,
            "total_price": 4000000,
            "message": "Đặt 2 phòng Suite (6 khách/phòng)"
        },
        {
            "room_type": "Family Room",
            "quantity": 2,
            "total_capacity": 10,
            "total_price": 3000000,
            "message": "Đặt 2 phòng Family (5 khách/phòng)"
        }
    ]
}
```

#### 5.2.3. Hiển thị số lượng phòng trống chính xác

**Rule:** Luôn hiển thị số lượng phòng trống thực tế theo thời gian đã chọn

**Chi tiết:**

- Khi Client lọc theo ngày, mỗi `RoomType` hiển thị:
    - `available_rooms`: Số lượng phòng trống
    - `total_rooms`: Tổng số phòng thuộc loại này
    - Ví dụ: "Còn 3/5 phòng trống"

**Implementation:**

```php
// Response structure
{
    "room_types": [
        {
            "id": 1,
            "name": "Deluxe Room",
            "price": 1000000,
            "max_guests": 2,
            "available_rooms": 3,
            "total_rooms": 5,
            "description": "Phòng cao cấp với view biển",
            "images": ["url1", "url2"]
        },
        {
            "id": 2,
            "name": "Suite Room",
            "price": 2000000,
            "max_guests": 4,
            "available_rooms": 1,
            "total_rooms": 2,
            "description": "Phòng suite sang trọng",
            "images": ["url1", "url2"]
        }
    ],
    "search_params": {
        "check_in": "2026-02-25",
        "check_out": "2026-02-27",
        "guests": 2
    }
}
```

---

## 6. Tóm tắt Business Rules

### 6.1. Ràng buộc xóa dữ liệu

| Entity    | Điều kiện KHÔNG được xóa | Message lỗi                                                           |
| --------- | ------------------------ | --------------------------------------------------------------------- |
| Role      | Đang được User sử dụng   | "Không thể xóa vai trò này vì đang có người dùng sử dụng"             |
| Staff     | Đã tạo Booking           | "Không thể xóa nhân viên này vì đã thực hiện các giao dịch đặt phòng" |
| Room Type | Có Room thuộc loại này   | "Không thể xóa loại phòng này vì đang có X phòng thuộc loại này"      |

### 6.2. Xác thực và bảo mật

| Feature            | Rule                             |
| ------------------ | -------------------------------- |
| Đăng nhập Client   | OTP 6 số qua Email, TTL 5 phút   |
| Email              | Unique, auto-fill nếu đã tồn tại |
| Cập nhật thông tin | Chỉ được phép khi đã đăng nhập   |

### 6.3. Booking Flow

| Bước               | Chi tiết                                                        |
| ------------------ | --------------------------------------------------------------- |
| 1. Tìm kiếm        | Theo ngày check-in/check-out, hiển thị số phòng trống chính xác |
| 2. Chọn loại phòng | Xem chi tiết room type                                          |
| 3. Chọn số khách   | Kiểm tra capacity, gợi ý nếu không đủ                           |
| 4. Đặt phòng       | Tạo booking, gửi invoice qua email                              |

### 6.4. Validation Checklist

- [ ] Role có User đang sử dụng không?
- [ ] Staff đã tạo Booking nào chưa?
- [ ] Room Type có Room nào thuộc loại này không?
- [ ] Email đã tồn tại trong hệ thống chưa?
- [ ] User đã xác thực OTP chưa (nếu cập nhật thông tin)?
- [ ] Phòng còn trống trong khoảng thời gian đã chọn?
- [ ] Số lượng khách có phù hợp với capacity của phòng?
- [ ] Đã gửi invoice qua email sau khi booking thành công?
