export default function phoneNumberFormat(number) {
    

    const firstSeq = number.substring(0, 3);
    const secondSeq = number.substring(3, 6);
    const lastSeq = number.substring(6, 11);

    return `(${firstSeq}) ${secondSeq}-${lastSeq}`;

}//