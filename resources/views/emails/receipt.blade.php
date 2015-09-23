<h1>Thanks for using Hom's Kitchen!</h1>
<hr />
Your order is as follows:<br><br>
Name: {{ $name }}<br>
Phone: {{ $phone }}<br>
@if ($email != "")
Email: {{ $email }}<br>
@endif
Pickup: {{ $location }}<br>
<br>
Ordered items:<br>
@foreach($itemArray as $item)
    {{ $item->qty }} x {{ $item->name }} = ${{ $item->itemTotal }}<br>
@endforeach
Total: ${{ $total }}<br><br>
@if ($notes != "")
Special notes: {{ $notes }}<br>
@endif
<br>
Thank you for using Hom's Kitchen!<br>
