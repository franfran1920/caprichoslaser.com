var el = wp.element.createElement;

wp.blocks.registerBlockType('pisol-edd/order-estimates', {

    title: 'Order estimate', // Block name visible to user

    category: 'woocommerce', // Under which category the block would appear

    parent: ['woocommerce/checkout-totals-block', 'woocommerce/cart-totals-block'],

    edit: function (props) {
        // How our block renders in the editor in edit mode
        return 'Order estimate date will be shown here';
    },

    save: function (props) {
        // How our block renders on the frontend
        return wp.element.createElement('div', { className: 'pi-edd-order-estimate', 'data-block-name': 'pisol-edd/order-estimate' }, '');
    }
});