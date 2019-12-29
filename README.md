# MyEvent: Venue Directory
Yii2 backend for MyEvent: Find a venue

## Database Structure

### User

* user_uuid
* user_name

### Venue

* venue_uuid
* user_uuid

### Admin

* admin_id
* admin_name
* admin_email
* admin_auth_key
* admin_password_hash
* admin_password_reset_token
* admin_status
* admin_created_at
* admin_updated_at
