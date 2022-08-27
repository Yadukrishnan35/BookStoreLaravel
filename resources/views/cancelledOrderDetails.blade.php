@component('mail::message')
#OrderCancelled

Hello {{$user}},<br>
Your order is cancelled successfully

OrderId : {{$orderId}}<br>
BookName : {{$bookName}}<br>
BookAuthor : {{$bookAuthor}}<br>
BookPrice : {{$bookPrice}}<br>
quantity : {{$quantity}}<br>
TotalPrice : {{$totalPrice}}<br>

<br>
{{config('app.name')}}
@endcomponent