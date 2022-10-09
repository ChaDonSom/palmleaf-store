<div x-data="{
  paypal: null,
  policy: @entangle('policy'),
  paymentElement: null,
  processing: false,
  error: null,
  handleSubmit() {
    this.processing = true
    this.error = null

    address = {
      city: '{{ $this->billing->city }}',
      country: '{{ $this->billing->country->iso2 }}',
      line1: '{{ $this->billing->line_one }}',
      line2: '{{ $this->billing->line_two }}',
      postal_code: '{{ $this->billing->postcode }}',
      state: '{{ $this->billing->state }}',
    }
  },
  init() {
    // Render the PayPal button into #paypal-button-container
    paypal.Buttons({
      style: {
        color: 'white',
        shape: 'pill',
      },
      // Call your server to set up the transaction
      createOrder: function(data, actions) {
        return fetch('/api/paypal/order/create', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            cart_id: '{{ $this->cart->id }}'
          })
        }).then(function(res) {
          //res.json();
          return res.json();
        }).then(function(orderData) {
          //console.log(orderData);
          return orderData.id;
        });
      },

      // Call your server to finalize the transaction
      onApprove: function(data, actions) {
        // Authorize the transaction
        actions.order.authorize().then(function(authorization) {
          // Get the authorization id
          var authorizationID = authorization.purchase_units[0].payments.authorizations[0].id
          // Call your server to validate and capture the transaction
          return fetch('/api/paypal/order/authorized', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              cart_id: '{{ $this->cart->id }}',
              paypal_order_id: data.orderID,
              paypal_authorization_id: authorizationID
            })
          }).then(res => {
            location.href = '{{ $returnUrl ?: url()->current() }}?paypal_order_id=' + data.orderID
          });
        });
      }
    }).render('#paypal-payment-element');
  }
}">
  <!-- Display a payment form -->
  <form x-ref="payment-form" x-on:submit.prevent="handleSubmit()">
    <div x-ref="paymentElement" id="paypal-payment-element">
      <!--Stripe.js injects the Payment Element-->
    </div>
    {{-- <button id="submit">
      <div class="hidden spinner" id="spinner"></div>
      <span id="button-text">Pay now</span>
    </button> --}}
    <div class="mt-4">
      <button
        class="flex items-center px-5 py-3 text-sm font-medium text-white bg-green-600 rounded-3xl hover:bg-green-500 disabled:opacity-50"
        type="submit"
        x-bind:disabled="processing"
      >
        <span
          x-show="!processing"
        >
          Make Payment
        </span>
        <span
          x-show="processing"
          class="block mr-2"
        >
          <svg
            class="w-5 h-5 text-white animate-spin"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
          >
            <circle
              class="opacity-25"
              cx="12"
              cy="12"
              r="10"
              stroke="currentColor"
              stroke-width="4"
            ></circle>
            <path
              class="opacity-75"
              fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
            ></path>
          </svg>
        </span>
        <span
          x-show="processing"
        >
          Processing
        </span>
      </button>
    </div>
    <div x-show="error" x-text="error" class="p-3 mt-4 text-sm text-red-600 rounded bg-red-50"></div>
  </form>
</div>