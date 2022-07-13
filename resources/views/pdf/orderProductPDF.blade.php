
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Products</title>
    <style>
        body{
            background-color: #F6F6F6;
            margin: 0;
            padding: 0;
        }
        h1,h2,h3,h4,h5,h6{
            margin: 0;
            padding: 0;
        }
        p{
            margin: 0;
            padding: 0;
        }
        .container{
            width: 80%;
            margin-right: auto;
            margin-left: auto;
        }
        .brand-section{
           background-color: #0d1033;
           padding: 50px 60px ;
        }
        .logo{
            width: 50%;
        }

        .row{
            display: flex;
            flex-wrap: wrap;
        }
        .col-6{
            width: 50%;
            flex: 0 0 auto;
        }
        .text-white{
            color: #fff;
        }
        .company-details{
            float: right;
            text-align: right;
        }
        .body-section{
            padding: 16px;
            border: 1px solid gray;
        }
        .heading{
            font-size: 20px;
            margin-bottom: 08px;
        }
        .sub-heading{
            color: #262626;
            margin-bottom: 05px;
        }
        table{
            background-color: #fff;
            width: 100%;
            border-collapse: collapse;
        }
        table thead tr{
            border: 1px solid #111;
            background-color: #f2f2f2;
        }
        table td {
            vertical-align: middle !important;
            text-align: center;
        }
        table th, table td {
            padding-top: 08px;
            padding-bottom: 08px;
        }
        .table-bordered{
            box-shadow: 0px 0px 5px 0.5px gray;
        }
        .table-bordered td, .table-bordered th {
            border: 1px solid #dee2e6;
        }
        .text-right{
            text-align: end;
        }
        .w-20{
            width: 20%;
        }
        .float-right{
            float: right;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="brand-section">
            <div class="row">
                <div class="col-6">
                    <h1 class="text-white">NXTYA STARTER KIT</h1>
                </div>
                <div class="col-6" style="padding-left: 200px;">
                    <div class="company-details">
                        <p class="text-white">Name:{{$order->user->name}}</p>
                        <p class="text-white">Email:{{$order->user->email}}</p>

                    </div>
                </div>
            </div>
        </div>

        <div class="body-section">
            <div class="row">
                <div class="col-6">
                    <h2 class="heading">Invoice No.: {{ $order['id'] }}</h2>
                    <p class="sub-heading">REFEREBC No.: {{ $order['reference'] }} </p>
                    <p class="sub-heading">Order Date: {{ $order['created_at'] }} </p>

                </div>
                <div class="col-6"  style="padding-left: 280px;">
                    <p class="sub-heading">Full Name:{{$order->client->first_name}} {{$order->client->last_name}}  </p>
                    <p class="sub-heading">Address: :{{$order->client->email}}   </p>
                    <p class="sub-heading">Phone Number:{{$order->client->phone}}   </p>

                </div>
            </div>
        </div>
        <?php $sum_tot_Price = 0 ?>
        <div class="body-section">
            <h3 class="heading">Ordered Items</h3>
            <br>
            <table class="table-bordered">
                <thead>
                    <tr>

                        <th class="w-18">Id</th>
                        <th class="w-18">Name</th>
                        <th class="w-18">Description</th>
                        <th class="w-18">Price</th>
                        <th class="w-18">Quantity</th>
                        <th class="w-18">Product Total Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order['products'] as $product)
                    <tr>
                        <th scope="row">{{ $product['id'] }}</th>
                        <td>{{ $product['name'] }}</td>
                        <td>{{ $product['description'] }}</td>
                        <td>{{ $product['price'] }}</td>

                        <td>{{ $product['pivot']['quantity'] }}</td>
                        <td>{{ $product['pivot']['total_price'] }}</td>

                    </tr>
                    <?php $sum_tot_Price += $product['pivot']['total_price'] ?>


                @endforeach
                </tbody>
            </table>
            <br>
            <h3 class="heading">Total Price to Paid : {{ $sum_tot_Price}}</h3>

        </div>

        <div class="body-section">
            <p>&copy; Copyright 2022 - NXTYA. All rights reserved.
                <a href="https://www.nxtya.com/" class="float-right">www.nxtya.com</a>
            </p>
        </div>
    </div>

</body>
</html>

