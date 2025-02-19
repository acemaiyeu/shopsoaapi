const checkbox_inputs = document.querySelectorAll('.input-value');
localStorage.clear();
localStorage.setItem('key',"")

var app = angular.module('myApp', []);
app.config(function($interpolateProvider) {
    $interpolateProvider.startSymbol('@{{').endSymbol('}}');
});

app.controller('ProductController', function($scope, $http) {

    scope.datas = [];
    $scope.products = [];
    $scope.message = "hl";
    // Lấy danh sách sản phẩm từ Laravel API
    console.log("run...")
    $http.get('localhost:8888/api/v0/productss?limit10').then(function(response) {
        $scope.products = response.data;
        console.log($scope.products)
    });

    // Thêm sản phẩm mới
    $scope.addProduct = function() {
        $http.post('/api/products', $scope.newProduct)
            .then(function(response) {
                $scope.products.push(response.data); // Cập nhật danh sách
                $scope.newProduct = {}; // Xóa dữ liệu input
            });
    };
   
});


function init(){
    let options = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        }
    }
    let params = "";
    let array_key = (localStorage.getItem('key').slice(0,-1)).split(",")
    array_key.forEach(e => {
        let item = localStorage.getItem(e)
        if(item != null){
            params += e.toLowerCase() + "=" + item + "&"
        }
    })
    callAPI("/api/v0/products?limit10" ,options)
    $scope.products = $scope.datas
}

checkbox_inputs.forEach(checkbox => {
    checkbox.addEventListener('click', function(){
        // alert("VALUE: " + value.value);
        
        let value = checkbox.value
        let array_value = value.split('-');
        let data = [];
        let key = array_value[0];
        let vl = array_value[1];
       
        if (localStorage.getItem(key) == null && key != null){
                data.push(vl);
                localStorage.setItem('key', localStorage.getItem('key') +  key + ",");
        }
        if (localStorage.getItem(key) != null){
            data = JSON.parse(localStorage.getItem(key));
            if (data.includes(vl)){
                    data.splice(vl, 1);
            }else{
                data.push(vl);  
            }
        }
        if(key != null){
            localStorage.setItem(key, JSON.stringify(data));
        }
        
        // console.log(localStorage.getItem(key));

    })
});

document.getElementById("btn-fillter").addEventListener('click', function(){
        let options = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        }
        let params = "";
        let array_key = (localStorage.getItem('key').slice(0,-1)).split(",")
        array_key.forEach(e => {
            let item = localStorage.getItem(e)
            if(item != null){
                params += e.toLowerCase() + "=" + item + "&"
            }
        })
        callAPI("/api/v0/products?limit10&" + params.slice(0,-1) ,options)
      
   
})

function callAPI(url, options){
    console.log("API: " + url);
    fetch(url, options)
    .then(response => response.json())  // Chuyển đổi phản hồi thành JSON
    .then(data => console.log(data),
        $scope.datas = data)    // Xử lý dữ liệu trả về
    .catch(error => console.log(error)); // Xử lý lỗi
}



