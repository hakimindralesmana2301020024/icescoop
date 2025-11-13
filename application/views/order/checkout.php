<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="checkout-page">
    <section class="checkout-hero">
        <div class="container checkout-hero-inner">
            <h1 class="checkout-hero-title">Checkout</h1>
            <div class="checkout-breadcrumb">
                <span class="breadcrumb-pill">Home&nbsp;&nbsp;/&nbsp;&nbsp;Cart&nbsp;&nbsp;/&nbsp;&nbsp;Checkout</span>
            </div>
        </div>
    </section>

    <section class="checkout-main">
        <div class="container checkout-main-inner">
            <div class="checkout-left">
                <div class="billing-box">
                    <h3 class="billing-title">Billing Address:</h3>
                    <form class="billing-form">
                        <div class="row">
                            <div class="col"><label>First name</label><input type="text" class="input" placeholder="First name"></div>
                            <div class="col"><label>Last name</label><input type="text" class="input" placeholder="Last name"></div>
                        </div>
                        <div class="row">
                            <div class="col"><label>Email address</label><input type="email" class="input" placeholder="Email address"></div>
                            <div class="col"><label>State</label>
                                <select class="input"><option>Select State</option><option>State 1</option></select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col"><label>City</label>
                                <select class="input"><option>Select City</option><option>City 1</option></select>
                            </div>
                            <div class="col"><label>Zip / postal code</label><input type="text" class="input" placeholder="Zip / postal code"></div>
                        </div>
                    </form>

                    <h4 class="payment-title">Payment Method:</h4>
                    <form class="payment-form">
                        <label class="radio-row"><input type="radio" name="pay" checked> Credit card <span class="card-icons"> <img src="<?php echo base_url('assets/images/cc-icons.png'); ?>" alt="cards"/></span></label>

                        <div class="field"><label>Card number</label><input type="text" class="input" placeholder="0000 0000 0000 0000"></div>

                        <div class="row small">
                            <div class="col"><label>Expiration date</label>
                                <div class="inline-selects">
                                    <select class="input small"><option>Month</option></select>
                                    <select class="input small"><option>Year</option></select>
                                </div>
                            </div>
                            <div class="col"><label>Security Code</label><input type="text" class="input" placeholder="CVV"></div>
                        </div>

                        <label class="radio-row"><input type="radio" name="pay"> Cash on Delivery</label>

                        <p class="small-note">By clicking the button, you agree to the Terms and Conditions</p>
                        <button class="btn order-now">Place Order Now</button>
                    </form>
                </div>
            </div>

            <aside class="checkout-right">
                <div class="summary-card">
                    <div class="summary-head">Items <span class="summary-price">Price</span></div>
                    <div class="summary-list">
                        <?php if(isset($cart) && is_array($cart)): foreach($cart as $c): ?>
                        <div class="summary-item"><div class="s-left"><span class="qty"><?php echo $c['qty']; ?>x</span> <span class="s-name"><?php echo $c['name']; ?></span></div><div class="s-right">$<?php echo $c['total']; ?></div></div>
                        <?php endforeach; endif; ?>
                    </div>
                    <div class="summary-total"><span>Grand Total</span><span class="accent">$<?php echo isset($summary['total']) ? $summary['total'] : '0.00'; ?></span></div>
                </div>
            </aside>
        </div>
    </section>
</div>
