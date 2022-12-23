export const useMoneyFormat = (amount) => {
    const formatter = new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "PHP",
    });

    return formatter.format(amount); /* $2,500.00 */
};
