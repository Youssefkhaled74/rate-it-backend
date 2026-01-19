# Subscriptions — Non-Technical Overview

This document explains the new Subscriptions feature in plain language for product, support, and QA teams.

- **What it is:** A paid subscriptions feature allowing users to subscribe to Monthly or Annual plans.
- **Free trial:** New subscribers receive a 6-month free trial (180 days) before billing begins.
- **Auto-renew:** Auto-renew is enabled by default. Users can cancel auto-renew; cancelling does not immediately remove access — access remains until the current paid period (or trial) ends.
- **Plans:** Plans are bilingual (English/Arabic) and include a name, description, price (in cents), currency, interval (monthly/annual), and trial length.

How users interact (high-level):
- View available plans (GET Plans).
- Start a subscription via Checkout (POST Checkout). For now the server supports a `manual` provider placeholder; payment gateway integrations (Stripe, Apple, Google) are planned.
- View current subscription (GET My Subscription).
- Cancel or resume auto-renew (POST Cancel Auto-Renew / POST Resume Auto-Renew).

Quick QA steps (Postman):
1. Run `GET /user/subscriptions/plans` and pick a `plan_id`.
2. `POST /user/subscriptions/checkout` with body `{ "plan_id": <id>, "provider": "manual" }` (requires auth).
3. `GET /user/subscriptions/me` to verify subscription state and trial dates.
4. `POST /user/subscriptions/cancel-auto-renew` to test cancelling; `POST /user/subscriptions/resume-auto-renew` to resume.

Run these locally after migrations and seeding:

```powershell
php artisan migrate
php artisan db:seed --class=SubscriptionPlansSeeder
```

Notes:
- The backend currently seeds Monthly and Annual plans with a 180-day trial. Payment processing is a manual placeholder; complete provider integration is required for live billing and renewals.

# Rate-It: User App - Non-Technical Documentation
**For Product Managers, Business Stakeholders & Non-Technical Teams**

**Version:** 1.0  
**Last Updated:** 2026-01-19  
**Audience:** Product, Business, Marketing, Customer Support

---

## Table of Contents

1. [What Rate-It Does](#what-rate-it-does)
2. [User Journey Stories](#user-journey-stories)
3. [Database Explained Like a Map](#database-explained-like-a-map)
4. [Rules & Policies](#rules--policies)
5. [Glossary](#glossary)

---

## 1. What Rate-It Does

### The Problem

Businesses struggle to collect **genuine, verified customer feedback** in a structured way. Traditional review systems suffer from:
- **Fake reviews:** Anyone can write a review without proving they visited
- **Scattered feedback:** Reviews spread across Google, social media, comment cards
- **No incentive:** Customers have no reason to spend time writing detailed reviews
- **Poor insights:** Generic star ratings don't reveal specific issues (food quality vs service vs ambiance)

### The Solution: Rate-It

**Rate-It** is a mobile-first review platform that solves these problems by:

1. **QR Code Verification:** Customers scan a unique QR code at the physical location, **proving they were there**
2. **Smart Questionnaires:** Each business type gets custom questions (e.g., restaurants ask about food quality, service speed, cleanliness)
3. **Points Rewards:** Customers earn loyalty points for every review, redeemable for vouchers
4. **Referral Program:** Invite friends and earn bonus points when they join
5. **Structured Data:** Businesses get detailed analytics on specific aspects, not just overall ratings

### Who Uses It?

**Mobile App Users (Customers):**
- Visit restaurants, cafes, clinics, shops, etc.
- Scan QR codes to leave reviews
- Earn and redeem points
- Discover new places and read authentic reviews

**Business Owners (Future Phase):**
- Generate QR codes for their branches
- View review analytics and customer feedback
- Respond to reviews
- Manage loyalty program settings

### Core Capabilities

✅ **Register & Login** — Phone-based registration with OTP verification  
✅ **Browse Brands** — Discover restaurants, cafes, clinics by category  
✅ **Scan QR Code** — Verify physical visit at branch  
✅ **Submit Reviews** — Answer custom questions + rate + add photos  
✅ **Earn Points** — Get loyalty points for reviews and referrals  
✅ **Invite Friends** — Refer friends via phone number and earn rewards  
✅ **View Notifications** — Get updates on points, reviews, rewards  
✅ **Manage Profile** — Update name, avatar, phone number  

### How It's Different

| Traditional Review Platforms | Rate-It |
|------------------------------|---------|
| Anyone can review (fake reviews) | **QR scan required** (verified visits) |
| Generic 1-5 star rating | **Custom questions** per business type |
| No rewards for reviewers | **Points & vouchers** for engagement |
| Limited to one platform (Google/Yelp) | **Centralized** multi-brand system |
| Text-only feedback | **Photos + structured answers** |

### Success Metrics

- **Customer Engagement:** Repeat review submissions per user
- **Verification Rate:** % of reviews with QR code proof
- **Referral Growth:** New users via invite program
- **Business Insights:** Actionable feedback on specific criteria (not just overall rating)

---

## 2. User Journey Stories

### Story 1: First-Time User Registration

**Character:** Sara, a 28-year-old marketing professional in Cairo

**Trigger:** Sara's friend Ahmed tells her about Rate-It and shares his referral link

**Steps:**
1. Sara downloads the Rate-It app from the App Store
2. She taps "Register" and enters:
   - Full name: "Sara Ahmed"
   - Phone number: +201000000002
   - Email: sara.ahmed@example.com
   - Birth date: 1996-03-15
   - Gender: Female
   - Nationality: Egypt
   - Password (secure)
   - Ahmed's phone number in "Invited by" field
3. App sends OTP to Sara's phone via SMS
4. Sara enters the 4-digit OTP code
5. Phone verified ✅
6. **Sara's account is created**
7. **Ahmed receives 50 bonus points** (referral reward)
8. Sara sees welcome screen with tutorial

**What Gets Stored:**
- Sara's profile in the system (name, phone, email, encrypted password)
- Verification status: phone verified = Yes
- Link between Sara and Ahmed (invite record)
- Points transaction: +50 points for Ahmed

**UI Screens:**
- Registration form (multi-step)
- OTP verification screen
- Welcome/onboarding screens

**Edge Cases:**
- Phone already registered → Error: "This phone number is already in use"
- Invalid OTP → Error: "Incorrect code, please try again" (max 3 attempts)
- Referral phone not found → Registration proceeds, no bonus awarded

---

### Story 2: Visiting a Restaurant and Leaving a Review

**Character:** Ahmed, a registered user who visits Starbucks

**Trigger:** Ahmed orders coffee at Starbucks City Center branch

**Steps:**
1. Ahmed finishes his coffee and sees a **QR code sticker** on the table
2. He opens Rate-It app and taps **"Scan QR"**
3. Camera opens → Ahmed scans the QR code
4. App shows:
   - "Starbucks City Center - Main Branch"
   - "You have 30 minutes to submit your review"
5. Ahmed taps **"Continue"**
6. App displays **custom questions** for cafes:
   - "How would you rate the coffee quality?" (1-5 stars)
   - "Was the service fast?" (Yes/No)
   - "What did you order?" (Multiple choice: Coffee/Tea/Food/Dessert)
7. Ahmed answers:
   - Coffee quality: 5 stars ⭐⭐⭐⭐⭐
   - Fast service: Yes ✓
   - Ordered: Coffee
8. Ahmed writes comment: "Excellent cappuccino!"
9. Ahmed uploads 2 photos of his coffee
10. Ahmed taps **"Submit Review"**
11. App shows success message:
    - "Review submitted! 🎉"
    - "You earned 50 points"
    - "Total balance: 350 points"

**What Gets Stored:**
- QR scan session (timestamp, branch, expires in 30 min)
- Review with overall rating, comment, photos
- Answer to each question (linked to specific criteria)
- Photos uploaded to cloud storage
- **50 points added** to Ahmed's loyalty balance
- **Notification sent** to Ahmed: "Review submitted successfully"

**UI Screens:**
- QR scanner camera
- Branch confirmation screen
- Question form (dynamic based on business type)
- Photo upload screen
- Success confirmation with points

**Edge Cases:**
- **Cooldown active:** Ahmed reviewed this branch 3 days ago, must wait 7 days → Error: "You can submit a new review on Jan 26"
- **Session expired:** Ahmed scans QR but waits 35 minutes → Error: "Session expired, please scan again"
- **QR code invalid:** Fake or corrupted QR → Error: "Invalid QR code"
- **Photo upload fails:** Review saves without photos, user notified

---

### Story 3: Checking Points and Redeeming a Voucher

**Character:** Sara, who has accumulated 500 points

**Trigger:** Sara wants to redeem her points for a discount voucher

**Steps:**
1. Sara opens Rate-It app and taps **"Points"** tab
2. She sees:
   - **Total Balance: 500 points**
   - "Points expiring soon: 50 points (Feb 15)"
   - Recent activity:
     - +50 points: Review at Starbucks (Jan 19)
     - +50 points: Friend joined via invite (Jan 18)
     - +100 points: Review at Burger King (Jan 15)
3. Sara taps **"Redeem Points"**
4. She selects: "100 EGP Voucher" (costs 400 points)
5. Confirms: "Redeem 400 points for 100 EGP voucher?"
6. Sara taps **"Confirm"**
7. App shows:
   - "Voucher redeemed! 🎁"
   - "New balance: 100 points"
   - "Check your email for voucher code"
8. Sara receives **email** with voucher code: VOUCHER-ABC123

**What Gets Stored:**
- Points transaction: -400 points (redemption)
- Voucher record (code, user, status: active)
- Updated balance: 100 points
- Notification: "You redeemed 400 points"

**UI Screens:**
- Points dashboard (balance, history, expiring soon)
- Voucher catalog (browse available rewards)
- Redemption confirmation modal
- Success screen with voucher details

**Edge Cases:**
- **Insufficient points:** Sara has 300 points, tries to redeem 400 → Error: "You need 400 points. Current balance: 300"
- **Points expired:** Some of Sara's old points expired → Balance recalculated, expired points removed
- **Voucher out of stock:** Selected voucher no longer available → Error: "This voucher is currently unavailable"

---

### Story 4: Receiving and Managing Notifications

**Character:** Ahmed, an active user with multiple actions

**Trigger:** Ahmed's app shows a red badge "3" on the notifications icon

**Steps:**
1. Ahmed taps **"Notifications"** bell icon
2. He sees 3 new notifications:
   - 🎉 **"Review Submitted"** (5 min ago)  
     "Your review for Starbucks has been submitted successfully."
   - 💰 **"Points Earned"** (5 min ago)  
     "You earned 50 points for your review! Total: 350 points"
   - 👥 **"Friend Joined"** (1 day ago)  
     "Sara joined Rate-It using your invite! You earned 50 bonus points."
3. Ahmed taps on the **"Friend Joined"** notification
4. App opens the **Invites** screen showing:
   - Sara Ahmed - Status: Joined ✓
   - Reward: 50 points (awarded)
5. Ahmed goes back to notifications and taps **"Mark All as Read"**
6. Badge disappears, all notifications now shown in gray (read)

**What Gets Stored:**
- Notification records (type, title, message, read status, timestamp)
- Read timestamps for each notification
- Unread count (used for badge number)

**UI Screens:**
- Notifications list (scrollable, with unread indicator)
- Notification detail view (tappable to see related content)
- Mark read buttons (single + mark all)
- Empty state: "No notifications yet"

**Edge Cases:**
- **No notifications:** User opens tab → Empty state with illustration
- **Notification deleted:** User swipes to delete → Removed from list permanently
- **Clear all:** User taps "Clear All" → Confirmation modal → All notifications deleted

---

### Story 5: Inviting Friends and Updating Profile

**Character:** Ahmed, who wants to refer his colleague Khaled

**Trigger:** Ahmed's colleague Khaled mentions he's looking for good cafes

**Part A: Invite Friend**

**Steps:**
1. Ahmed opens Rate-It app and taps **"Invites"** tab
2. He taps **"Invite Friends"** button
3. App shows: "Invite friends and earn 50 points per friend!"
4. Ahmed enters Khaled's phone: +201000000005
5. Ahmed taps **"Check Status"**
6. App shows: "Khaled is not on Rate-It yet (You can invite)"
7. Ahmed taps **"Send Invite"**
8. App shows success: "Invite sent! You'll earn 50 points when Khaled joins"
9. Ahmed sees invite in "My Invites" list:
   - Khaled (+201000000005)
   - Status: Pending ⏳
   - Reward: 50 points (not yet earned)

**Part B: Update Profile**

**Steps:**
1. Ahmed wants to update his profile photo
2. He taps **"Profile"** tab
3. He taps on the avatar placeholder
4. Selects **"Take Photo"** from camera
5. Takes a selfie and confirms
6. Photo uploads → Success: "Profile photo updated!"
7. Ahmed also decides to update his phone number
8. He taps **"Change Phone"**
9. Enters new phone: +201111111111
10. Taps **"Send OTP"**
11. Receives SMS with code: 5678
12. Enters OTP and confirms
13. App shows: "Phone number updated successfully!"
14. Ahmed's new phone is now verified

**What Gets Stored (Invites):**
- Invite record: Ahmed → Khaled (phone: +201000000005)
- Status: pending
- When Khaled registers → Status changes to "joined", Ahmed gets 50 points

**What Gets Stored (Profile):**
- Avatar image uploaded to cloud storage (CDN URL saved)
- Phone change request (OTP hash, expiration time)
- After verification: User phone updated, old requests deleted

**UI Screens:**
- Invites list (pending, joined, rewards earned)
- Invite friends form (phone input, check status)
- Profile view (avatar, name, phone, email, edit buttons)
- Change phone flow (new phone input → OTP screen → confirmation)

**Edge Cases:**
- **Friend already registered:** Ahmed tries to invite Sara (already a user) → Message: "Sara is already on Rate-It"
- **Duplicate invite:** Ahmed invites Khaled twice → Error: "You already invited this phone number"
- **Invalid OTP:** Ahmed enters wrong code 3 times → Request expired, must resend OTP
- **New phone already used:** Ahmed's new phone belongs to another user → Error: "This phone number is already registered"

---

## 3. Database Explained Like a Map

Think of the database as a **filing cabinet** where every drawer stores different types of information. Here's what's in each drawer:

### 📁 **Users Drawer**
- **What it stores:** Customer accounts
- **Information kept:**
  - Full name, phone number, email
  - Password (encrypted, secure)
  - Birth date, gender, nationality
  - Profile photo (if uploaded)
  - Phone verification status (Yes/No)
  - Registration date
- **Why it matters:** This is the master list of all app users. Without a user record, you can't log in or submit reviews.

---

### 📁 **Categories & Subcategories Drawers**
- **What they store:** Business types organized in a hierarchy
- **Information kept:**
  - **Categories:** Food & Beverages, Healthcare, Retail, Entertainment
  - **Subcategories:** Restaurants, Cafes, Medical Clinics, Pharmacies, Gyms, etc.
  - Names in English and Arabic
  - Category logos/icons
- **Why it matters:** Users browse by category to find businesses. Each subcategory has custom review questions (cafes ask about coffee quality, clinics ask about wait time).

---

### 📁 **Brands, Places, and Branches Drawers**
- **What they store:** Business locations organized in 3 levels
- **Information kept:**
  - **Brand:** Company name (e.g., "Starbucks"), logo, points expiration policy
  - **Place:** Specific location (e.g., "Starbucks City Center"), description, city, area
  - **Branch:** Physical address (e.g., "Main Branch, 123 Main St"), GPS coordinates, working hours, **unique QR code**
- **Why it matters:** This is the directory of all businesses in the app. Users scan QR codes at branches to prove they visited.
- **Example hierarchy:**
  - Brand: Starbucks
    - Place: Starbucks City Center (Cairo)
      - Branch 1: Main Branch (QR: BRANCH_123_QR_XYZ)
      - Branch 2: Mall Wing (QR: BRANCH_456_QR_ABC)

---

### 📁 **Rating Criteria & Questions Drawer**
- **What it stores:** Custom questions for each business type
- **Information kept:**
  - Question text (English/Arabic)
  - Question type:
    - **Rating Scale:** 1-5 stars (e.g., "Food quality?")
    - **Yes/No:** True/False (e.g., "Was service fast?")
    - **Multiple Choice:** Select from options (e.g., "What did you order?" → Coffee/Tea/Food)
  - Required or optional
- **Why it matters:** Different businesses need different feedback. Restaurants ask about food, clinics ask about doctor professionalism. Each subcategory gets its own question set.

---

### 📁 **Reviews, Answers, and Photos Drawers**
- **What they store:** Customer feedback submitted after QR scan
- **Information kept:**
  - **Review:** Who submitted it (user), where (branch), when (timestamp), overall rating (1-5), comment text
  - **Answers:** Response to each question (rating value, yes/no, chosen option)
  - **Photos:** Up to 3 images uploaded with review
- **Why it matters:** This is the core value—authentic, verified feedback from real customers. Business owners analyze this to improve service.
- **Example:**
  - Review #123 by Ahmed at Starbucks Main Branch (Jan 19, 2026)
    - Overall rating: 4.5 stars
    - Comment: "Excellent cappuccino!"
    - Answers:
      - Coffee quality: 5 stars
      - Service speed: Yes
      - Ordered: Coffee
    - Photos: 2 images of coffee

---

### 📁 **QR Sessions Drawer**
- **What it stores:** Active QR scan sessions (temporary, expires after 30 minutes)
- **Information kept:**
  - Who scanned (user)
  - Where scanned (branch)
  - When scanned (timestamp)
  - **Session token** (unique code, like a ticket)
  - Expiration time (30 minutes from scan)
  - Used or not (consumed after review submission)
- **Why it matters:** Prevents fake reviews. The session token proves "Ahmed was at Starbucks Main Branch at 10:00 AM." Token expires or gets used once, so it can't be reused.

---

### 📁 **Points & Transactions Drawer**
- **What it stores:** Loyalty points system—like a bank account for each user
- **Information kept:**
  - **Points Settings:** How many points per review (50), per referral (50), expiration rules
  - **Transactions:** Every point earned or spent
    - +50 points: Review submitted (Jan 19)
    - +50 points: Friend joined via invite (Jan 18)
    - -400 points: Voucher redeemed (Jan 20)
  - Expiration dates (points earned in Jan 2026 expire in Jan 2027)
- **Why it matters:** Incentivizes user engagement. Users accumulate points like frequent flyer miles and redeem for rewards.
- **Balance calculation:** Add all positive transactions (earned), subtract negative (redeemed), exclude expired.

---

### 📁 **Invites Drawer**
- **What it stores:** Referral tracking (who invited whom)
- **Information kept:**
  - Inviter (Ahmed)
  - Invited phone number (+201000000005)
  - Status: Pending / Joined / Rejected
  - Reward points (50)
  - Date invited
  - Date joined (if completed)
- **Why it matters:** Tracks referral rewards. When Khaled registers using Ahmed's referral, the system finds this record and awards Ahmed 50 points.

---

### 📁 **Notifications Drawer**
- **What it stores:** Messages sent to users (like a mailbox)
- **Information kept:**
  - Recipient (user)
  - Type (review submitted, points earned, friend joined)
  - Title + message text
  - Read or unread status
  - Timestamp
- **Why it matters:** Keeps users informed about their activity. The red badge number on the app icon comes from counting unread notifications here.

---

### 📁 **Phone Verification Drawer**
- **What it stores:** Temporary OTP codes for phone/password verification
- **Information kept:**
  - Phone number
  - OTP code (encrypted)
  - Type (registration, forgot password, change phone)
  - Expiration (10 minutes)
  - Attempts count (max 3)
- **Why it matters:** Security. Ensures only the real phone owner can verify. After 10 minutes or 3 failed attempts, OTP becomes invalid.

---

### 📁 **Lookups Drawers** (Genders, Nationalities)
- **What they store:** Static dropdown options for registration
- **Information kept:**
  - **Genders:** Male, Female, Other (with English/Arabic names)
  - **Nationalities:** Egypt, Saudi Arabia, UAE, etc. (with country codes and flag URLs)
- **Why it matters:** Provides consistent choices for user profiles. No free text, easier for analytics (e.g., "80% of users from Egypt").

---

### How These Drawers Connect (Simplified)

```
👤 USER (Ahmed)
  ├── 📝 REVIEWS (5 reviews written)
  │    ├── REVIEW #1 at Starbucks → BRANCH → PLACE → BRAND
  │    ├── ANSWERS (coffee quality: 5, service: yes)
  │    └── PHOTOS (2 images)
  ├── 💰 POINTS (350 points balance)
  │    ├── TRANSACTION #1: +50 from Review #1
  │    └── TRANSACTION #2: +50 from inviting Sara
  ├── 👥 INVITES (invited Sara, Khaled)
  │    ├── Sara: Status=Joined ✓, Rewarded=50 points
  │    └── Khaled: Status=Pending ⏳
  └── 🔔 NOTIFICATIONS (12 total, 3 unread)
```

---

## 4. Rules & Policies

### Review Submission Rules

**Cooldown Period:**
- **What it is:** Minimum waiting time before submitting another review at the same branch
- **Why:** Prevents spam and ensures reviews reflect real visits over time
- **How it works:**
  - Each branch sets its own cooldown (0, 7, 30, or 90 days)
  - Example: Starbucks Main Branch has 7-day cooldown
  - If Ahmed reviewed on Jan 19, he can't review again until Jan 26
- **User sees:** "You can submit a new review on [date]"

**QR Code Session Expiration:**
- **What it is:** Time limit after scanning QR code
- **Duration:** 30 minutes
- **Why:** Ensures reviews are submitted immediately after visit, not days later from home
- **What happens after expiration:**
  - User must rescan QR code to get new session
  - Old session token becomes invalid

**Photo Upload Limits:**
- **Maximum photos per review:** 3 images
- **File size:** Max 5MB per photo
- **Formats allowed:** JPG, PNG, WEBP, GIF
- **Why:** Balances storage costs with user desire to share visuals

**Answer Requirements:**
- **Required questions:** Must be answered to submit (marked with red asterisk in app)
- **Optional questions:** Can be skipped
- **Validation:**
  - Rating scale: Must select 1-5 stars
  - Yes/No: Must choose one
  - Multiple choice: Must select at least one option (if required)

---

### Points & Loyalty Rules

**Earning Points:**
- **Per review:** 50 points (configurable by admin)
- **Per successful invite:** 50 points (when invited friend registers)
- **Admin adjustments:** Manual additions/deductions by support team (rare cases)

**Points Expiration:**
- **Default expiration:** 365 days from earning date
- **Brand-specific rules:** Some brands may set different expiration (e.g., Starbucks: 180 days)
- **Grace period:** None—points expire exactly on expiration date
- **Notification:** Users get notification 30 days before points expire ("50 points expiring soon!")

**Balance Calculation:**
- **What counts:** All earned points that haven't expired yet
- **What doesn't count:** Expired points, redeemed points
- **Example:**
  - Ahmed earned 500 points total
  - 100 points expired last month
  - 150 points redeemed for voucher
  - **Current balance:** 250 points

**Redemption Rules:**
- **Minimum redemption:** 100 points (configurable)
- **Processing:** Instant—voucher code sent immediately
- **Refunds:** No refunds once redeemed (points permanently deducted)
- **Voucher validity:** 90 days from redemption date

---

### Invite/Referral Rules

**Who Can Be Invited:**
- Only phone numbers **not registered** on Rate-It
- Must be valid phone numbers (international format)

**Duplicate Invites:**
- You cannot invite the same phone number twice
- If friend already joined, they don't appear in "can be invited" list

**Reward Conditions:**
- **Inviter gets points when:**
  - Invited friend registers using inviter's phone in "Invited by" field
  - Invite status changes to "Joined"
  - Points awarded only once per invite
- **Inviter does NOT get points if:**
  - Friend registers without entering inviter's phone
  - Friend was already registered before invite

**Invite Expiration:**
- Currently: Invites **never expire** (pending indefinitely)
- Future feature: Invites may expire after 90 days if unused

---

### Phone Verification Rules

**OTP (One-Time Password) Requirements:**
- **Code length:** 4-6 digits (random)
- **Delivery:** SMS to provided phone number
- **Validity:** 10 minutes from sending
- **Max attempts:** 3 tries to enter correct code
- **After expiration or max attempts:** Must request new OTP

**When OTP is Required:**
- Registration (phone verification)
- Forgot password (identity verification)
- Changing phone number (security check)

**Security Measures:**
- OTP codes stored encrypted (hashed)
- Rate limiting: Max 1 OTP request per minute
- IP tracking (future): Prevent mass OTP spam

---

### Account & Profile Rules

**Registration Requirements:**
- Full name (required)
- Phone number (required, unique, E.164 format: +201234567890)
- Email (required, unique, valid format)
- Password (minimum 8 characters, required)
- Birth date (required, must be 13+ years old)
- Gender (required, select from list)
- Nationality (required, select from list)

**Password Policy:**
- Minimum length: 8 characters
- Recommended: Mix of uppercase, lowercase, numbers, symbols
- Encrypted using bcrypt (industry standard)

**Profile Photo:**
- Optional (default avatar used if not uploaded)
- Max size: 5MB
- Formats: JPG, PNG
- Stored in cloud (CDN for fast loading)

**Phone Number Changes:**
- Must verify new phone with OTP
- Cannot change to phone already registered by another user
- Old phone number released (can be used by new user after change)

---

### Data Privacy & Security

**User Data Protection:**
- Passwords: Encrypted (never stored in plain text)
- OTP codes: Hashed (cannot be reverse-engineered)
- Personal data: Not shared with third parties without consent

**Account Deletion:**
- User can request account deletion via support
- Reviews remain visible but anonymized ("Deleted User")
- Points balance forfeited (non-transferable)

**Data Retention:**
- Active accounts: Data kept indefinitely
- Inactive accounts (no login for 2+ years): May be archived
- Deleted accounts: Personal data removed within 30 days

---

## 5. Glossary

### **Branch**
A physical location of a business where customers can visit and scan QR codes. Example: "Starbucks Main Branch at 123 Main Street." Each branch has its own QR code and can set its own review cooldown period.

---

### **Brand**
The company or chain name that owns multiple places/locations. Example: "Starbucks" is a brand that has many places (City Center, Mall, Airport). Brands control points expiration policies for their locations.

---

### **Cooldown**
A waiting period before a user can submit another review at the same branch. Prevents spam. Example: If cooldown is 7 days and you reviewed on Monday, you can't review again until next Monday.

---

### **Criteria / Rating Criteria**
The specific questions users answer when reviewing a business. There are three types:
1. **Rating Scale:** 1-5 stars (e.g., "How was the food quality?")
2. **Yes/No:** True or false (e.g., "Was the place clean?")
3. **Multiple Choice:** Select from options (e.g., "What did you order?")

---

### **Invite / Referral**
When an existing user asks a friend to join Rate-It by providing the friend's phone number. If the friend registers, the inviter earns bonus points (usually 50 points).

---

### **Notification**
A message sent to the user's app about important events. Examples: "Review submitted," "Points earned," "Friend joined via your invite." Appears in the notifications tab with a red badge showing unread count.

---

### **OTP (One-Time Password)**
A temporary code (like "1234" or "5678") sent to your phone via SMS for verification. Used during registration, password reset, and phone number changes. Valid for 10 minutes and can be used only once.

---

### **Place**
A specific business location associated with a brand and category. Example: "Starbucks City Center" is a place. A place can have multiple branches (Main Branch, Mall Wing, etc.).

---

### **Points / Loyalty Points**
Virtual currency earned by users for actions like submitting reviews and inviting friends. Can be redeemed for vouchers or discounts. Points expire after a certain period (usually 1 year).

---

### **Points Balance**
The total number of points a user currently has available to redeem. Calculated by adding earned points and subtracting redeemed/expired points.

---

### **Points Transaction**
A record of points earned or spent. Types include:
- **EARN_REVIEW:** Points gained from review (+50)
- **EARN_INVITE:** Points gained from referral (+50)
- **REDEEM_VOUCHER:** Points spent on reward (-100)
- **EXPIRE:** Points that expired (-50)

---

### **QR Code**
A square barcode displayed at business locations (on stickers, table tents, posters). Users scan it with the app camera to prove they visited. Each branch has a unique QR code. Example: "BRANCH_123_QR_XYZ789"

---

### **Review**
Customer feedback about a visit to a branch. Includes:
- Overall rating (1-5 stars)
- Text comment (optional)
- Answers to custom questions
- Photos (up to 3)
- Timestamp (when submitted)

---

### **Review Score**
An average calculated from all rating-scale answers in a review. Example: If a user rated "Food Quality" as 5 stars, "Service" as 4 stars, and "Ambiance" as 5 stars, the review score is (5+4+5)/3 = 4.67. Different from overall rating (user's general impression).

---

### **Session Token**
A temporary access key generated when scanning a QR code. Acts like a ticket valid for 30 minutes. Required to submit a review. Expires after 30 minutes or after use (single-use). Example: "sess_abc123xyz"

---

### **Subcategory**
A specific type of business within a broader category. Example: Category "Food & Beverages" contains subcategories "Restaurants," "Cafes," "Fast Food." Each subcategory has custom review questions.

---

### **Voucher**
A discount code earned by redeeming points. Example: Redeem 400 points for a "100 EGP voucher." Sent to user's email or shown in the app. Valid for a limited time (usually 90 days).

---

### **E.164 Format**
An international standard for phone numbers. Format: `+[country_code][number]`. Example: `+201234567890` (Egypt), `+966501234567` (Saudi Arabia). Always starts with `+` and country code.

---

### **Idempotency**
A technical term meaning "doing the same action twice has the same result as doing it once." Example: If the app tries to award points for the same review twice due to a bug, the system detects duplicate and awards points only once.

---

### **Multipart Form Data**
A method for uploading files (like photos) along with text data in a form. Used when submitting reviews with images. Behind the scenes, the app packages text answers + photos together and sends them to the server.

---

### **Status Code (HTTP)**
A three-digit number indicating if a request succeeded or failed:
- **200 OK:** Success
- **401 Unauthorized:** Not logged in or invalid token
- **422 Validation Error:** Form data incorrect (e.g., missing required field)
- **500 Server Error:** Something broke on the server (rare, logged for debugging)

---

### **Token / Bearer Token**
A secure access key given to users after login. Like a digital key card. The app includes this token in every request to prove "this is Ahmed's account." Expires after inactivity or logout.

---

## Missing Clarity & Open Questions

The following topics are **not fully defined** in the current system and may need product/business decisions:

1. **Home Screen Content:** What banners/promotions appear? How are they managed?
2. **Onboarding Flow:** What screens do new users see on first launch? Tutorial steps?
3. **Voucher Catalog:** What vouchers are available for redemption? How are they sourced (partnerships)?
4. **Review Moderation:** Are reviews auto-published or manually reviewed by admins?
5. **Push Notifications:** How are users notified outside the app (Firebase? OneSignal?)
6. **User Levels/Tiers:** Future: Bronze/Silver/Gold tiers based on activity?
7. **Gamification:** Badges, achievements, leaderboards?
8. **Social Features:** Can users follow each other? Like reviews?
9. **Business Owner Dashboard:** When can businesses see their reviews and analytics?
10. **Multi-language Support:** Is Arabic fully translated or just structure in place?

---

**Document Version:** 1.0  
**For Questions Contact:** Product Team @ product@rateit.example.com  
**Last Reviewed:** 2026-01-19
