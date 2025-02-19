@vite(['resources/css/content.css', 'resources/js/content.js', 'resources/js/app.js', 'resources/js/angular.min.js'])
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.8.2/angular.min.js"></script>
<div ng-app="myApp">
      <div ng-controlelr='ProductController'>
           
            {{-- @foreach ($data['data'] as $product)
            <h4> Tên sản phẩm: {{$product['name']}}</h4>
                  {{-- <h1> Mã:  {{ $product['code']}} </h1>
                  <h4> Tên sản phẩm: {{$product['name']}}</h4>
                  {{-- <h4> Giá sản phẩm: {{$product['price']}}</h4> --}} 
            {{-- @endforeach --}}
                  <div class="row">
                        <div class="col-3 fillter">
                              <span  class="col-9 title-fillter">Fillter</span>
                              <span  class="col-3 btn btn-success" id="btn-fillter">Lọc</span>
                              @foreach ($fillters as $fillter)
                                    <form method="post" action="{{env("URL_API") . "/v0/fillter/products"}}" class="label-fillter">
                                          @foreach ($fillter['datas'] as $key => $f)
                                                      <label for="" class="lable-fillter-title">{{$key}}</label> <br/>
                                                      @foreach ($f as $f1) 
                                                      <input class="input-value" type="checkbox" value={{$key ."-". $f1}}> {{$f1}} <br/>
                                                      @endforeach     
                                          @endforeach
                                    </form>
                              @endforeach
                        </div>
                        <div class="col-6">
                                    <div ng-repeat="item in products">
                                          {{-- {{item}} --}}
                                    </div>
                        </div>
                        <div class="col-3">
                                    phải
                        </div>      
                  </div>
      </div>
</div>