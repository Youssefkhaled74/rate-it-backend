# دورة المراجعة التقنية (Review Cycle) - مرجع عربي

هذا المستند يشرح دورة الـ Review بشكل تقني داخل مشروع Rate It، من لحظة مسح QR حتى إدارة المراجعة من لوحة الأدمن، مع توضيح الجداول والحقول المستخدمة في كل خطوة.

## 1. نظرة عامة على الـ Flow

1. المستخدم يسجل الدخول في التطبيق.
2. المستخدم يمسح QR الخاص بالفرع.
3. النظام ينشئ Session في `branch_qr_sessions`.
4. المستخدم يفتح شاشة التقييم ويرسل `overall_rating + answers + photos`.
5. النظام يتحقق من:
   - صلاحية QR session
   - حالة الاشتراك
   - cooldown للفرع
   - صحة الإجابات حسب نوع كل سؤال
6. النظام يحفظ:
   - `reviews`
   - `review_answers`
   - `review_photos` (اختياري)
   - `review_answer_photos` (لسؤال PHOTO)
7. النظام يستهلك الـ session (`consumed_at`).
8. النظام يحسب `review_score`.
9. النظام يضيف نقاط في `points_transactions`.
10. الأدمن يراجع/يخفي/يرد/يميز من لوحة التحكم.

## 2. الجداول الأساسية في دورة المراجعة

### 2.1 `branch_qr_sessions`
- الغرض: ربط Scan Session بالمستخدم والفرع ومنع إعادة الاستخدام.
- أعمدة مهمة:
  - `user_id`
  - `branch_id`
  - `qr_code_value`
  - `session_token` (Unique)
  - `scanned_at`
  - `expires_at`
  - `consumed_at` (Nullable)

### 2.2 `reviews`
- الغرض: سجل المراجعة الأساسي.
- أعمدة مهمة:
  - `user_id`
  - `place_id`
  - `branch_id`
  - `overall_rating`
  - `comment`
  - `status` (`ACTIVE` / `DELETED_BY_ADMIN`)
  - `review_score`
  - حقول الموديريشن:
    - `is_hidden`, `hidden_reason`, `hidden_at`, `hidden_by_admin_id`
    - `admin_reply_text`, `replied_at`, `replied_by_admin_id`
    - `is_featured`, `featured_at`, `featured_by_admin_id`

### 2.3 `review_answers`
- الغرض: إجابات أسئلة التقييم لكل Review.
- أعمدة مهمة:
  - `review_id`
  - `criteria_id`
  - `rating_value` (RATING)
  - `yes_no_value` (YES_NO)
  - `choice_id` (MULTIPLE_CHOICE)
- قيود مهمة:
  - `unique(review_id, criteria_id)` لمنع تكرار نفس السؤال داخل نفس المراجعة.

### 2.4 `review_photos`
- الغرض: صور عامة مرفقة بالمراجعة.
- أعمدة:
  - `review_id`
  - `storage_path`
  - `encrypted`

### 2.5 `review_answer_photos`
- الغرض: صور مرتبطة بإجابة سؤال معين (نوع PHOTO).
- أعمدة:
  - `review_answer_id`
  - `storage_path`
  - `encrypted`

### 2.6 `rating_criteria`
- الغرض: تعريف أسئلة التقييم لكل Subcategory.
- أعمدة مهمة:
  - `subcategory_id`
  - `type` (`RATING`, `YES_NO`, `MULTIPLE_CHOICE`, ... حسب النسخة الحالية)
  - `is_required`
  - `sort_order`
  - `weight`
  - `points`
  - `yes_value`, `no_value`
  - `yes_weight`, `no_weight`

### 2.7 `rating_criteria_choices`
- الغرض: اختيارات أسئلة الـ Multiple Choice.
- أعمدة:
  - `criteria_id`
  - `choice_text` / `choice_en` / `choice_ar`
  - `value` (درجة الاختيار)
  - `weight` (وزن الاختيار)
  - `sort_order`
  - `is_active`

### 2.8 `subscriptions`
- الغرض: التحكم في أحقية المستخدم في إنشاء Review.
- أعمدة مهمة:
  - `user_id`
  - `status`
  - `started_at`
  - `free_until`
  - `paid_until`

### 2.9 `subscription_settings`
- الغرض: إعدادات الاشتراك العام (مثل مدة المجاني).
- أعمدة:
  - `free_trial_days`
  - `is_active`

### 2.10 `points_transactions`
- الغرض: دفتر حركة نقاط المستخدم.
- أعمدة مهمة:
  - `user_id`
  - `brand_id`
  - `type`
  - `points`
  - `reference_type`
  - `reference_id`
  - `expires_at`
  - `meta`

## 3. التنفيذ التقني خطوة بخطوة (مع قراءة/كتابة DB)

### 3.1 Scan QR
- قراءة:
  - `branches` (للتحقق من QR والفرع)
- كتابة:
  - `branch_qr_sessions` بسجل جديد token-based

### 3.2 Submit Review Request
- الطلب يشمل:
  - `session_token`
  - `overall_rating`
  - `comment`
  - `answers[]`
  - `photos[]` (اختياري)
  - `answerPhotos[criteria_id][]` (اختياري)

### 3.3 Pre-Validation
- قراءة `branch_qr_sessions` للتحقق من:
  - token موجود
  - يخص نفس `user_id`
  - `consumed_at IS NULL`
  - `expires_at > now`
- قراءة `subscriptions` + `subscription_settings`:
  - السماح فقط إذا trial/paid ساري
  - إنشاء FREE subscription تلقائي عند أول مرة (عند عدم وجود سجل)
- قراءة `branches.review_cooldown_days`
- قراءة `reviews` لاستخراج آخر تقييم لنفس الفرع/المستخدم
- قراءة `rating_criteria` + `rating_criteria_choices` للتحقق من:
  - required criteria
  - data type correctness

### 3.4 Transaction: إنشاء المراجعة
- Begin DB Transaction
- كتابة في `reviews`
- كتابة في `review_answers`
- كتابة في `review_photos` (اختياري)
- كتابة في `review_answer_photos` (اختياري)
- تحديث `branch_qr_sessions.consumed_at = now()`

### 3.5 حساب `review_score`
- قراءة `review_answers` مع `criteria` و `choice`
- حساب الأوزان والدرجات
- تحديث `reviews.review_score`

### 3.6 Award Points
- استدعاء:
  - `awardPointsForReview`
  - `awardPointsForReviewAnswers`
- كتابة في `points_transactions`
- ملاحظة:
  - فشل إضافة النقاط لا يفشل إنشاء المراجعة (يتم تسجيل الخطأ فقط)

### 3.7 Commit + Response
- Commit Transaction
- إعادة البيانات:
  - review
  - points_awarded
  - points_awarded_answers
  - points_balance

## 4. معادلة حساب التقييم (`review_score`)

يوجد تقييمان في النظام:

1. `overall_rating`
- قيمة يدخلها المستخدم مباشرة من الواجهة.

2. `review_score`
- قيمة محسوبة من إجابات الأسئلة وأوزانها.

### 4.1 استخراج `score` لكل إجابة
- `RATING`:
  - `score = rating_value`
  - `answerWeight = 1`
- `YES_NO`:
  - Yes: `score = yes_value`, `answerWeight = yes_weight`
  - No: `score = no_value`, `answerWeight = no_weight`
- `MULTIPLE_CHOICE`:
  - `score = choice.value`
  - `answerWeight = choice.weight`
- `TEXT` و `PHOTO`:
  - لا يدخلان مباشرة في `review_score` (في المنطق الحالي)

### 4.2 الوزن الفعلي
- `effectiveWeight = criteria.weight * answerWeight`

### 4.3 النتيجة النهائية
- إذا `sumWeights > 0`:
  - `review_score = round(sum(score * effectiveWeight) / sumWeights, 2)`
- إذا `sumWeights = 0`:
  - fallback لمتوسط بسيط للـ scores المتاحة

## 5. دور الأدمن في دورة المراجعة

من Admin Reviews:

1. عرض قائمة المراجعات + فلاتر (تاريخ/حالة/تقييم/براند/فرع).
2. عرض التفاصيل:
  - بيانات المستخدم
  - الإجابات
  - الصور
  - حالة الإخفاء/الرد/التمييز
3. `Hide / Unhide`:
  - عند الإخفاء، `hidden_reason` إجباري.
4. `Reply`:
  - تحديث `admin_reply_text`, `replied_at`, `replied_by_admin_id`.
5. `Feature / Unfeature`:
  - تحديث `is_featured`, `featured_at`, `featured_by_admin_id`.

## 6. حالات الرفض والاستثناءات (Error Matrix)

1. QR غير صالح / ليس لنفس المستخدم / مستهلك
- النتيجة: رفض
- كود شائع: `422`

2. QR منتهي
- النتيجة: رفض + بيانات `expires_at` و `server_time`
- كود: `422`

3. الفرع غير موجود
- النتيجة: رفض

4. الاشتراك منتهي
- النتيجة: رفض إنشاء Review جديد
- كود: `402`

5. cooldown لم ينتهِ
- النتيجة: رفض + `retry_after_seconds` + `cooldown_ends_at`
- كود: `429`

6. سؤال مطلوب غير مُرسل
- النتيجة: رفض
- كود: `422`

7. نوع إجابة غير مطابق
- أمثلة:
  - rating خارج `1..5`
  - yes/no invalid
  - choice_id لا ينتمي للسؤال
  - text فارغ
  - photo question بدون صور
- النتيجة: رفض

8. فشل رفع الصور أثناء transaction
- النتيجة: rollback كامل للمراجعة

9. فشل points service
- النتيجة: Review ينجح
- النقاط لا تُضاف (مع تسجيل Log)

## 7. ملاحظات هندسية مهمة

1. يوجد ازدواج في مفهوم التقييم:
- `overall_rating` (UI/User input)
- `review_score` (Engine-calculated)

2. لازم Product Decision واضح:
- هل التقارير والترتيب تعتمد على `overall_rating`؟
- أم `review_score`؟
- أم الاثنين (عرضي/تحليلي)؟

3. تأكد من توافق enum/value في `points_transactions.type` مع الأنواع المستخدمة فعليًا في الكود (مثال `EARN_REVIEW_ANSWERS`).

4. تأكد من توافق أعمدة `review_answers` مع كل الأنواع النشطة (خصوصًا `text_value` لو مستخدم في runtime).

## 8. Checklist اختبار الدورة (QA سريع)

1. Scan QR صالح -> إنشاء session.
2. إعادة استخدام نفس session بعد إرسال Review -> يجب يفشل.
3. Review أثناء trial -> ينجح.
4. Review بعد انتهاء trial وبدون paid -> يفشل.
5. cooldown branch = 0 -> يسمح دائمًا.
6. cooldown branch > 0 -> يمنع التقييم المتكرر قبل المدة.
7. إجابات ناقصة لسؤال required -> يفشل.
8. Multiple-choice choice_id خاطئ -> يفشل.
9. صور أكثر من الحد -> يفشل حسب policy.
10. Review ناجح + فشل points intentionally -> Review يظل محفوظ.

