
export default function currencyFormater(number) {
    const formater = Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    });

    return formater.format(number);
}