@component('mail::message')
#OrderInvoice

Hello {{$user}},<br>
Your order is placed successfully


OrderId : {{$orderId}}<br>
BookName : {{$bookName}}<br>
BookAuthor : {{$bookAuthor}}<br>
BookPrice : {{$bookPrice}}<br>
quantity : {{$quantity}}<br>
TotalPrice : {{$totalPrice}}<br>

<br>
{{config('app.name')}}
@endcomponent