const { registerCheckoutBlock, __experimentalRegisterCheckoutFilters } = wc.blocksCheckout;

const OrderEstimate = ({ object }) => {
    if (object && object.extensions && object.extensions.pisol_edd && object.extensions.pisol_edd.order_estimate) {
        return wp.element.createElement("p", {
            className: 'pisol-fees-container'
        }, object.extensions.pisol_edd.order_estimate);
    } else {
        return null;
    }
}

const options = {
    metadata: {
        name: 'pisol-edd/order-estimates',
        parent: ["woocommerce/checkout-totals-block", 'woocommerce/cart-totals-block'],
    },
    component: (object) => React.createElement(OrderEstimate, { object: object }, "A Function Component")

};


const productEstimate = (value, extensions, args) => {

    if (args?.context === 'cart' && args?.cart?.extensions?.pisol_edd?.product_estimate_cart == false) return value;

    if (args?.context === 'summary' && args?.cart?.extensions?.pisol_edd?.product_estimate_checkout == false) return value;

    if (args?.cartItem?.extensions?.pisol_edd?.product_estimate) {
        return value + '<br>' + args.cartItem.extensions.pisol_edd.product_estimate;
    }

    return value;
}

__experimentalRegisterCheckoutFilters('pisol-edd', {
    itemName: productEstimate
});



registerCheckoutBlock(options);