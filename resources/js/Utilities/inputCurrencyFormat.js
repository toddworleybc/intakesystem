import currencyFormater from "./currencyFormater";

export default function inputCurrencyFormat(input) {

    const value = currencyFormater(input);

    const formatedValue = value.replaceAll(/[$,]/g, "");

    return formatedValue;
}//