Subscription module

Dev notes:
- Trial rules: plans seeded with `trial_days = 180` (approx. 6 months). During trial, subscription_status = 'trialing' and `free_until` holds trial end.
- Auto-renew: `auto_renew` defaults to true; cancelling auto-renew sets `auto_renew=false` and `canceled_at` timestamp. Subscription remains active until `paid_until`.
- Renewal window: auto-renew must be turned off at least 24 hours before period end (business rule; enforcement is expected in provider integration or cron job).
- Provider placeholder: current implementation uses 'manual' provider and creates a pending transaction record. Integrate real providers (Stripe/Apple/Google) by updating `SubscriptionService::checkout` and processing provider webhooks.
