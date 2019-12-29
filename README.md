# MyEvent: Venue Directory

Yii2 backend for MyEvent: Find a venue

## Plan B: Launch Pivots of same codebase

* Real-estate listing app
* Chalet-listing app
* Car-listing app
* 4-sale kind of deal (Sell everything)

## Database Structure

### User

* user_uuid
* user_name
* user_email
* user_auth_key
* user_password_hash
* user_password_reset_token
* user_status
* user_created_at
* user_updated_at

### Occasion

Occasion for an event. Such as wedding, birthday, valentines, etc.

* occasion_uuid
* occasion_name

### Venue

* venue_uuid
* user_uuid [the owner of this venue]
* venue_name
* venue_location (Google places autocomplete?)
* venue_location_longitude
* venue_location_latitude
* venue_description [text]
* venue_approved [boolean] - "Did admin approve for this to be displayed on app?"
* venue_contact_email
* venue_contact_phone
* venue_contact_website
* venue_capacity_minimum [integer]
* venue_capacity_maximum [integer]
* venue_operating_hours [text]
* venue_restrictions [text] "No smoking, cant bring your own food"


#### Venue_Occasion

Relation between venue and occasion

* venue_uuid
* occasion_uuid


#### Venue_Photo

Photos of a venue

* photo_uuid
* venue_uuid
* photo_url
* photo_created_datetime

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
