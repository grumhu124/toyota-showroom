// Tái hiện: đăng ký tài khoản mới → đặt lịch → ghi lại toàn bộ lỗi xảy ra.
export default async function run(page, ui) {
  const results = {};
  const errors = [];

  // 1. Vào trang đăng ký.
  const r1 = await page.goto('http://127.0.0.1:8000/dang-ky');
  results.registerPage = { status: r1.status() };

  // 2. Điền form đăng ký.
  const stamp = Date.now();
  const email = `testuser${stamp}@example.test`;
  await page.locator('input[name="name"]').fill('Nguyễn Test');
  await page.locator('input[name="email"]').fill(email);
  await page.locator('input[name="phone"]').fill('0907778899');
  await page.locator('input[name="password"]').fill('password123');
  await page.locator('input[name="password_confirmation"]').fill('password123');
  await page.locator('button[type="submit"]').click();
  await page.waitForLoadState('networkidle');
  results.afterRegister = { url: page.url(), email, body: (await page.locator('body').innerText()).slice(0, 300) };

  // 3. Đặt lịch với tài khoản vừa tạo.
  await page.goto('http://127.0.0.1:8000/dat-lich');
  await page.waitForLoadState('networkidle');
  await page.locator('input[name="type"][value="viewing"]').check();
  await page.locator('select[name="car_id"]').selectOption({ index: 2 });
  await page.locator('input[name="contact_name"]').fill('Nguyễn Test');
  await page.locator('input[name="contact_phone"]').fill('0907778899');
  const future = new Date(Date.now() + 2 * 86400000);
  const when = `${future.getFullYear()}-${String(future.getMonth() + 1).padStart(2, '0')}-${String(future.getDate()).padStart(2, '0')}T10:30`;
  await page.locator('input[name="requested_at"]').fill(when);
  await page.locator('form.appointment-card button[type="submit"]').click();
  await page.waitForLoadState('networkidle');
  results.afterBooking = {
    url: page.url(),
    flash: await page.locator('.appointment-notice').first().innerText().catch(() => 'NO FLASH'),
    fieldErrors: await page.locator('.field-error').allInnerTexts(),
  };
  results.consoleErrors = errors;
  return results;
}
