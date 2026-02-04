@component('mail::message')
# New Order Placed

An order has been placed on Palmleaf Store.

**Order Reference:** {{ $order->reference }}  
**Order Total:** {{ $order->total->formatted }}  
**Status:** {{ ucfirst($order->status) }}  
**Placed At:** {{ $order->placed_at->format('F j, Y, g:i a') }}

@if($order->user)
**Customer:** {{ $order->user->name }} ({{ $order->user->email }})
@endif

@component('mail::button', ['url' => config('app.url') . '/admin'])
View Order in Admin
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
