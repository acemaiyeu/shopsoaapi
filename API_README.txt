Mục lục
	1. Người dùng
		1.1 Api đăng ký (register account)
		1.2 Api đăng nhập (login account)
		1.3 Api thông tin (profile account)
		1.4 Api đăng xuất (logout account)
		1.5 Api cập nhật thông tin (Update Account)




#1 Người dùng
#1.1 Api đăng ký (register account)
Request: /api/auth/register
Method: POST
BODY: {
	fullname: "Khách hàng 1",
	email: "guest1@gmail.com"
	password: 123456
       }
Response: 
	{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0Ojg4ODgvYXBpL2F1dGgvcmVnaXN0ZXIiLCJpYXQiOjE3NDQwOTY5NjksImV4cCI6MTc0NDcwMTc2OSwibmJmIjoxNzQ0MDk2OTY5LCJqdGkiOiJlMHVsR294ajFwNnNnb2plIiwic3ViIjoiMiIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.i-DzWnKvvD_e7j3_GfV5eT3O5_zRgRvhyhtLMc6bQYk",
	"token_type": "bearer",
	"expires_in": 604800
	}
#1.2 Api đăng nhập (login account)
Request: /api/auth/login
Method: POST
BODY: {
	email: "guest1@gmail.com"
	password: 123456
       }
Response: 
	{
	"access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0Ojg4ODgvYXBpL2F1dGgvbG9naW4iLCJpYXQiOjE3NDQwOTcxMTMsImV4cCI6MTc0NDcwMTkxMywibmJmIjoxNzQ0MDk3MTEzLCJqdGkiOiIwTU12WVVYRWZVeEhGSksyIiwic3ViIjoiMiIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.qFE9g5g6F80xQxEnRlRniqjnt-tSiSD3QJ4lcxPluIE",
	"token_type": "bearer",
	"expires_in": 604800
}

#1.3 Api thông tin (profile account)


- Request: /api/auth/profile
- Method: GET
- Headers: Bearer Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0Ojg4ODgvYXBpL2F1dGgvcmVnaXN0ZXIiLCJpYXQiOjE3NDQwOTczNjQsImV4cCI6MTc0NDcwMjE2NCwibmJmIjoxNzQ0MDk3MzY0LCJqdGkiOiI1dmlBOHVYMUdKMXZPV05mIiwic3ViIjoiNCIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.WZZbqga-MsWwbBhfrwzY4iD5_O7e_9fN6E8_48NBcT0
===== Thành công
- Response: 200
	 {
	"data": {
		"id": 4,
		"fullname": "Khách hàng 1",
		"email": "guest@gmail.com",
		"phone": null,
		"city": "1",
		"district": null,
		"ward": null,
		"role": "Khách hàng",
		"created_at": "08/04/2025 07:29"
	}
}
==== Thất bại
- Response: 404
	{
	"status": 404,
	"message": "Không tìm thấy người dùng"
	}



#1.4 Api đăng xuất (logout account)
- Reqest: /api/auth/logout
- Method: POST
- Headers: Bearer Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0Ojg4ODgvYXBpL2F1dGgvcmVnaXN0ZXIiLCJpYXQiOjE3NDQwOTczNjQsImV4cCI6MTc0NDcwMjE2NCwibmJmIjoxNzQ0MDk3MzY0LCJqdGkiOiI1dmlBOHVYMUdKMXZPV05mIiwic3ViIjoiNCIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.WZZbqga-MsWwbBhfrwzY4iD5_O7e_9fN6E8_48NBcT0
- Response:{
		"message": "Successfully logged out"
	   }	
#1.5 Api cập nhật thông tin (Update Account)
- Reqest: /api/auth/profile
- Method: PUT
- Headers: Bearer Token: eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0Ojg4ODgvYXBpL2F1dGgvcmVnaXN0ZXIiLCJpYXQiOjE3NDQwOTczNjQsImV4cCI6MTc0NDcwMjE2NCwibmJmIjoxNzQ0MDk3MzY0LCJqdGkiOiI1dmlBOHVYMUdKMXZPV05mIiwic3ViIjoiNCIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.WZZbqga-MsWwbBhfrwzY4iD5_O7e_9fN6E8_48NBcT0
=== Thành công: 
- Response: 200
	{
	"data": {
		"id": 4,
		"fullname": "Khách hàng 1",
		"email": "guest@gmail.com",
		"phone": null,
		"city": "1",
		"district": null,
		"ward": null,
		"role": "Khách hàng",
		"created_at": "08/04/2025 07:29"
		}
	}

=== Thất bại
- Response: 404
	{
		"status": 404,
		"message": "Không tìm thấy người dùng"
	}	
#2 Loại Sản phẩm (Category)
#2.1 Lấy danh sách 
- Request: /api/v0/categories
- Param: ?code=&name
- Method: GET
- Response: 200
{
	"data": [
		{
			"id": 1,
			"code": "THEME-LARAVEL",
			"name": "Theme LARAVEL framework",
			"created_at": "08-04-2025",
			"created_by": "Thành Huy"
		},
		{
			"id": 2,
			"code": "THEME-SPRING-BOOT",
			"name": "Theme Springboot",
			"created_at": "08-04-2025",
			"created_by": "Thành Huy"
		},
        ....
    ]
}
#2.2 Lấy chi tiết loại sản phẩm (Detail Category)
- Request: /api/v0/category/1
- Param: id = 1
- Method: GET
- Response: 200
{
	"data": {
		"id": 1,
		"code": "THEME-LARAVEL",
		"name": "Theme LARAVEL framework",
		"created_at": "08-04-2025",
		"created_by": "Thành Huy"
	}
}
#2.3 Tạo chi tiết loại sản phẩm (Create Category)
- Request: /api/v1/category
- Method: POST
- Body: {
	"code": "THEME-SPRING-BOOT 2",
	"name": "Theme Spring boot"
}
- Response: 200
{
	"data": {
		"id": 10,
		"code": "THEME-SPRING-BOOT 2",
		"name": "Theme Spring boot",
		"created_at": "08-04-2025",
		"created_by": "Khách hàng 1"
	}
}   
 
- Response: 400 
{
	"message": "Bạn không có quyền truy cập api này"
}
- Response: 422
{
	"errors": {
		"code": [
			"Tên danh mục bắt buộc nhập."
		],
		"name": [
			"Tên danh mục bắt buộc nhập."
		]
	},
	"message": "Validation Failed"
}


#2.4 Cập nhật chi tiết loại sản phẩm (Update Category)
- Request: /api/v1/category
- Method: PUT
- Body: {
	"code": "THEME-SPRING-BOOT",
	"name": "Theme Spring boot 23",
	"id": 10
}
- Response: 200
{
	"data": {
		"id": 10,
		"code": "THEME-SPRING-BOOT 2",
		"name": "Theme Spring boot 23",
		"created_at": "08-04-2025",
		"created_by": "Khách hàng 1"
	}
}
 
- Response: 400 
{
	"message": "Bạn không có quyền truy cập api này"
}
- Response: 422
{
	"errors": {
		"id": [
			"Mã danh mục bắt buộc nhập."
		]
	},
	"message": "Validation Failed"
}
#2.4 Xóa chi tiết loại sản phẩm (Delete Category)
- Request: /api/v1/category/10
- Method: DELETE
- Param: id = 10
- Response: 200
{
	"status": 200,
	"message": "Xóa danh sách thành công"
}
- Response: 404
{
	"status": 404,
	"message": "Không tìm thấy dữ liệu"
}