# MoMo Startup Checklist (Windows)

Dung cho truong hop project chay bang `php artisan serve` tren `localhost:8000`.

## 1. Mo terminal tai thu muc project

```bash
cd C:/Users/GIGABYTE/Desktop/Project_Web2/Hotel-Management
```

## 2. Bat database (XAMPP)

- Mo XAMPP Control Panel.
- Start `MySQL`.
- Neu dung Apache cho viec khac thi bat them `Apache` (khong bat buoc cho `artisan serve`).

## 3. Cap nhat dependencies (chi khi can)

```bash
composer install
npm install
```

## 4. Chay Laravel + Vite

```bash
composer run dev
```

## 5. Bat ngrok cho cong 8000

Mo terminal moi:

```bash
ngrok http 8000
```

Lay URL HTTPS o dong `Forwarding`, vi du:

`https://abcxyz.ngrok-free.dev -> http://localhost:8000`

## 6. Cap nhat `.env`

Sua bien:

```env
NGROK_URL=https://abcxyz.ngrok-free.dev
```

## 7. Clear cache config cua Laravel

```bash
php artisan optimize:clear
```

## 8. Dam bao migration da day du

```bash
php artisan migrate --force
```

## 9. Test nhanh callback route

- Return URL: `/booking/momo-return`
- IPN URL: `/api/payment/momo-ipn`

Hai URL day du duoc tao tu `NGROK_URL`:

- `{NGROK_URL}/booking/momo-return`
- `{NGROK_URL}/api/payment/momo-ipn`

## 10. Test flow thanh toan

- Vao web: `http://localhost:8000`
- Chon phong -> Checkout -> Payment -> Bam thanh toan MoMo.
- Neu khong nhay qua QR, mo log de xem loi:

```bash
# Git Bash
 tail -n 200 storage/logs/laravel.log
```

Hoac mo file log truc tiep:

`storage/logs/laravel.log`

---

## Ghi nho nhanh

- Moi lan restart may + ngrok free: thuong phai lay URL moi va sua lai `NGROK_URL`.
- Doi `NGROK_URL` xong phai chay `php artisan optimize:clear`.
- Neu web chay cong 80 (XAMPP), dung `ngrok http 80` thay vi `8000`.
