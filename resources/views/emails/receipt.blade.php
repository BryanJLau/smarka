<h1>Thanks for using Hom's Kitchen!</h1>
<hr />
Your order is as follows:<br><br>
Name: {{ $name }}<br>
Address: {{ $address }}<br>
Phone: {{ $phone }}<br>
<br>
Ordered items:<br>
@foreach($itemArray as $item)
    {{ $item->qty }} x {{ $item->name }} = ${{ $item->itemTotal }}<br>
@endforeach
Total: ${{ $total }}<br>
<br>
Thank you for using Hom's Kitchen!<br>
