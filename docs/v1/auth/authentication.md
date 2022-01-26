# Authentication 

**URL** : `/api/auth/login`

**Method** : `POST`

**Parameter**
```json
{
	"email" : "email",
	"password" : "password"
}
```

## Success Response
**Code**: `200`

**Response**
```json
{
    "token" : "string",
    "refresh_token" : "string",
    "user" : "object",
    "message" : "string"
}
```

## Parameter Error Response
**Code**: `422`

