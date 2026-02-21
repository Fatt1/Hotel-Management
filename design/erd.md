# Cấu trúc Database MySQL cho Laravel

## 1. room_types

```sql
CREATE TABLE room_types (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(100) NOT NULL,
    adult_quantity INT NOT NULL,
    child_quantity INT NOT NULL,
    single_bed_quantity INT NOT NULL,
    double_bed_quantity INT NOT NULL,
    width DECIMAL(8,2) NOT NULL,
    height DECIMAL(8,2) NOT NULL,
    hourly_price DECIMAL(10,2) NOT NULL,
    daily_price DECIMAL(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Laravel Model:** `public $timestamps = false;` (không sử dụng timestamps)

---

## 2. floors

```sql
CREATE TABLE floors (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Laravel Model:** `public $timestamps = false;` (không sử dụng timestamps)

---

## 3. rooms

```sql
CREATE TABLE rooms (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_type_id INT UNSIGNED NOT NULL,
    floor_id INT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL UNIQUE,
    status ENUM('ready','maintenance', 'cleaning') NOT NULL DEFAULT 'ready',
    FOREIGN KEY (room_type_id) REFERENCES room_types(id) ON DELETE CASCADE,
    FOREIGN KEY (floor_id) REFERENCES floors(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Laravel Model:** `public $timestamps = false;` (không sử dụng timestamps)

**Trạng thái Status:**

- `ready`: Phòng sẵn sàng sử dụng
- `maintenance`: Phòng đang bảo trì
- `cleaning`: Phòng đang dọn dẹp

---

## 4. equipment_categories

```sql
CREATE TABLE equipment_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Laravel Model:** `public $timestamps = false;` (không sử dụng timestamps)

---

## 5. equipments

```sql
CREATE TABLE equipments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    equipment_category_id INT UNSIGNED NOT NULL,
    import_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (equipment_category_id) REFERENCES equipment_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Laravel Model:** `public $timestamps = false;` (không sử dụng timestamps)

---

## 6. room_equipment

```sql
CREATE TABLE room_equipment (
    room_type_id INT UNSIGNED NOT NULL,
    equipment_id INT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    PRIMARY KEY (room_type_id, equipment_id),
    FOREIGN KEY (room_type_id) REFERENCES room_types(id) ON DELETE CASCADE,
    FOREIGN KEY (equipment_id) REFERENCES equipments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Laravel Model:** `public $timestamps = false;` (không sử dụng timestamps)

---

## 7. system_settings

```sql
CREATE TABLE system_settings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(255) NOT NULL,
    setting_value TEXT NOT NULL,
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Laravel Model:** `public $timestamps = false;` (không sử dụng timestamps)

## 9. customers

```sql
CREATE TABLE customers (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    country VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Laravel Model:** `public $timestamps = false;` (không sử dụng timestamps)

---

## 10. roles

```sql
CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Laravel Model:** `public $timestamps = false;` (không sử dụng timestamps)

---

## 11. staff

```sql
CREATE TABLE staff (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id INT UNSIGNED NOT NULL,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    email VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    password VARCHAR(255) NOT NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Laravel Model:** `public $timestamps = false;` (không sử dụng timestamps)

---

## 12. service_groups

```sql
CREATE TABLE service_groups (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Laravel Model:** `public $timestamps = false;` (không sử dụng timestamps)

---

## 13. services

```sql
CREATE TABLE services (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    group_id INT UNSIGNED NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    unit VARCHAR(50) NOT NULL,
    FOREIGN KEY (group_id) REFERENCES service_groups(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Laravel Model:** `public $timestamps = false;` (không sử dụng timestamps)

---

## 14. bookings

```sql
CREATE TABLE bookings (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_id INT UNSIGNED NOT NULL,
    booking_date DATETIME NOT NULL,
    staff_id INT UNSIGNED NULL,
    total_service_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    total_room_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    surcharge_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    final_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Laravel Model:** `public $timestamps = false;` (không sử dụng timestamps)

---

## 15. booking_details

```sql
CREATE TABLE booking_details (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id INT UNSIGNED NOT NULL,
    booking_id INT UNSIGNED NOT NULL,
    checkin_date DATETIME NOT NULL,
    checkout_date DATETIME NOT NULL,
    status ENUM('pending', 'checked_in', 'checked_out', 'cancelled', 'no_show') NOT NULL DEFAULT 'pending',
    hourly_price DECIMAL(10,2) NOT NULL,
    daily_price DECIMAL(10,2) NOT NULL,
    service_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    surcharge_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Laravel Model:** `public $timestamps = false;` (không sử dụng timestamps)

**Trạng thái Status:**

- `pending`: Đang chờ xử lý
- `checked_in`: Đã check-in
- `checked_out`: Đã check-out
- `cancelled`: Đã hủy
- `no_show`: Khách không đến

---

## 16. service_usages

```sql
CREATE TABLE service_usages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_detail_id INT UNSIGNED NOT NULL,
    service_id INT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (booking_detail_id) REFERENCES booking_details(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Laravel Model:** Sử dụng timestamps mặc định của Laravel

---

## 17. role_claims

```sql
CREATE TABLE role_claims (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    claim_name VARCHAR(255) NOT NULL,
    claim_value VARCHAR(255) NOT NULL,
    role_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Laravel Model:** `public $timestamps = false;` (không sử dụng timestamps)

---

## 18. maintenance_tickets

```sql
CREATE TABLE maintenance_tickets (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_id INT UNSIGNED NOT NULL,
    equipment_id INT UNSIGNED NULL,
    reported_date DATE NOT NULL,
    issue_description TEXT NOT NULL,
    technician_note TEXT,
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    repair_cost DECIMAL(10,2) NOT NULL DEFAULT 0,
    reported_by_staff_id INT UNSIGNED NOT NULL,
    technician_id INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
    FOREIGN KEY (equipment_id) REFERENCES equipments(id) ON DELETE SET NULL,
    FOREIGN KEY (reported_by_staff_id) REFERENCES staff(id) ON DELETE CASCADE,
    FOREIGN KEY (technician_id) REFERENCES staff(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Laravel Model:** Sử dụng timestamps mặc định của Laravel

**Trạng thái Status:**

- `pending`: Chờ xử lý
- `in_progress`: Đang sửa chữa
- `completed`: Đã hoàn thành
- `cancelled`: Đã hủy

---

## 19. room_type_images

```sql
CREATE TABLE room_type_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_type_id INT UNSIGNED NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    order INT NOT NULL DEFAULT 0,
    FOREIGN KEY (room_type_id) REFERENCES room_types(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Laravel Model:** `public $timestamps = false;` (không sử dụng timestamps)

---

## 20. amenities

```sql
CREATE TABLE amenities (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    icon VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Laravel Model:** `public $timestamps = false;` (không sử dụng timestamps)

---

## 21. room_type_amenities

```sql
CREATE TABLE room_type_amenities (
    room_type_id INT UNSIGNED NOT NULL,
    amenity_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (room_type_id, amenity_id),
    FOREIGN KEY (room_type_id) REFERENCES room_types(id) ON DELETE CASCADE,
    FOREIGN KEY (amenity_id) REFERENCES amenities(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Laravel Model:** `public $timestamps = false;` (không sử dụng timestamps)

---

## 22. payments

```sql
CREATE TABLE payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id INT UNSIGNED NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    note TEXT,
    transaction_code VARCHAR(255),
    staff_id INT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (staff_id) REFERENCES staff(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Laravel Model:** Sử dụng timestamps mặc định của Laravel

---

## 23. surcharge_policies

```sql
CREATE TABLE surcharge_policies (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_type_id INT UNSIGNED NOT NULL,
    policy_type VARCHAR(100) NOT NULL,
    hour_mark DECIMAL(5,2) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (room_type_id) REFERENCES room_types(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Laravel Model:** `public $timestamps = false;` (không sử dụng timestamps)

---

## Tổng kết

### Các bảng KHÔNG sử dụng timestamps (cần thêm `public $timestamps = false;` trong Model):

1. room_types
2. floors
3. rooms
4. equipment_categories
5. equipments
6. room_equipment
7. system_settings
8. accounts
9. customers
10. roles
11. staff
12. service_groups
13. services
14. bookings
15. booking_details
16. role_claims
17. room_type_images
18. amenities
19. room_type_amenities
20. surcharge_policies

### Các bảng SỬ DỤNG timestamps (Laravel mặc định):

1. service_usages (có created_at, updated_at)
2. maintenance_tickets (có created_at, updated_at)
3. payments (có created_at, updated_at)

### Các trường ENUM và ý nghĩa:

#### rooms.status

- `ready`: Phòng sẵn sàng sử dụng
- `maintenance`: Phòng đang bảo trì
- `cleaning`: Phòng đang dọn dẹp

#### booking_details.status

- `pending`: Chờ xử lý
- `checked_in`: Đã check-in
- `checked_out`: Đã check-out
- `cancelled`: Đã hủy
- `no_show`: Khách không đến

#### maintenance_tickets.status

- `pending`: Chờ xử lý
- `in_progress`: Đang sửa chữa
- `completed`: Đã hoàn thành
- `cancelled`: Đã hủy
