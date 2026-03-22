# Ke hoach trien khai gui email qua queue

## 1) Thong tin da chot
- Nha cung cap mail: SMTP (Gmail/host rieng)
- Queue driver production: database
- Cach chay worker: queue:work duoi Supervisor
- Nghiep vu gui mail uu tien:
  - Reset mat khau
  - Gui email khi dat online thanh cong
- Chinh sach do tin cay job:
  - tries = 3
  - timeout = 120 giay
  - backoff tang dan

## 2) Muc tieu ky thuat
- Khi ung dung tao mail, request HTTP tra ve nhanh, khong doi gui xong.
- Tat ca mail di qua queue job.
- Co co che retry, log loi, theo doi failed jobs va cho phep retry lai.

## 3) Kien truc de xuat
- Dung Mailable + ShouldQueue cho tung loai email.
- Queue connection: database.
- Worker doc queue mail chay nen lien tuc.
- Tach queue mail theo ten queue rieng (vi du: emails) de de quan ly.

## 4) Cac buoc implement

### Buoc 1: Cau hinh .env va queue
- Dat QUEUE_CONNECTION=database.
- Cau hinh SMTP that (MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_ENCRYPTION).
- Chay cac lenh:
  - php 

### Buoc 2: Chuan hoa Mailables
- Tao hoac cap nhat cac Mailable:
  - BookingSuccessMail
  - PaymentInvoiceMail
  - ResetPasswordMail (neu dang custom luong reset)
- Cho cac mailable implement ShouldQueue.
- Dat queue ten emails (onQueue('emails')).

### Buoc 3: Day mail vao queue tai nghiep vu
- Tai cac action/controller lien quan:
  - Dat online thanh cong: dispatch BookingSuccessMail qua Mail::to(...)->queue(...)
  - Thanh toan/hoa don: queue PaymentInvoiceMail
  - Reset mat khau: queue notification/mail reset
- Dam bao cac luong loi khong lam fail request chinh (ghi log va thong bao hop ly).

### Buoc 4: Retry, timeout, backoff
- Dat thuoc tinh tren mailable/job:
  - public tries = 3
  - public timeout = 120
  - public backoff = [10, 60, 180]
- Bo sung failed() handler de ghi log ngu canh.

### Buoc 5: Van hanh worker voi Supervisor
- Tao cau hinh Supervisor cho queue worker:
  - command: php artisan queue:work database --queue=emails,default --sleep=3 --tries=3 --timeout=120
  - autostart/autorestart bat
  - numprocs theo tai nguyen server
- Reload Supervisor va kiem tra trang thai process.

### Buoc 6: Monitoring va thao tac su co
- Theo doi bang jobs va failed_jobs.
- Bo sung lenh van hanh:
  - php artisan queue:failed
  - php artisan queue:retry all hoac theo ID
  - php artisan queue:flush (chi dung khi can)

## 5) Ke hoach test
- Test 1: Trigger dat phong online thanh cong, xac nhan request tra ve nhanh va co ban ghi trong jobs.
- Test 2: Worker chay, mail duoc gui thanh cong, ban ghi jobs bien mat.
- Test 3: Co tinh sai SMTP, job bi retry va vao failed_jobs dung chinh sach.
- Test 4: Retry lai job failed thanh cong sau khi sua SMTP.
- Test 5: Thanh toan tao mail hoa don di vao queue emails dung y.

## 6) Tieu chi hoan thanh
- Khong con luong gui mail dong bo trong request chinh.
- 3 nghiep vu uu tien deu gui mail qua queue.
- Co worker production on dinh va tai lieu van hanh co ban.
- Co the truy vet va xu ly failed mail jobs.

## 7) Rollout de xuat
- Phase 1: Booking online thanh cong.
- Phase 2: Hoa don/thanh toan.
- Phase 3: Reset mat khau.
- Moi phase deploy rieng, theo doi 24-48h roi moi mo rong phase tiep.

## 9) Tien do thuc te
- [x] Phase 1 da implement:
  - Tao `BookingSuccessMail` theo kieu queue (`ShouldQueue`) va day vao queue `emails`.
  - Dispatch mail tai luong `BookingCheckoutController@confirm` bang `Mail::to(...)->queue(...)`.
  - Bo sung truyen `email_verify` tu form checkout sang confirm de co dia chi nhan mail.
  - Da tao bang queue `jobs` va `failed_jobs` bang migration.
- [ ] Phase 2 chua thuc hien.
- [ ] Phase 3 chua thuc hien.

## 8) Ghi chu
- Neu sau nay can throughput cao hon, co the chuyen sang Redis + Horizon ma khong doi business flow.

## Câu lệnh chạy worker
php artisan queue:work database --queue=emails,default --tries=3 --timeout=120
