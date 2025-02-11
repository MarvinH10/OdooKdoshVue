export const generateContent6 = (item, style) => `
    <div style="
        width: 230px;
        height: 80px;
        text-align: center;
        position: relative;
        padding: 4px;
        background-color: white;
        // border: 1px solid #ccc;
        color: black;
        font-family: Figtree, ui-sans-serif, system-ui, sans-serif,
            'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';">

        <div style="font-size: 1.25rem; line-height: 1.75rem; font-weight: 500; margin-top: 0.25rem; margin-right: 5rem;">
            S/ ${item.price}
        </div>

        <div style="font-size: 0.5rem; line-height: 1.25; margin-right: 62px; margin-left: 0.25rem;">
            ${item.categ_id}
        </div>

        <div style="font-size: 0.45rem; line-height: 1.25; margin-right: 72px;">
            ${item.default_code || "\u00A0"}
        </div>

        <div style="font-size: 0.45rem; line-height: 1.25; margin-right: 72px;">
            ${item.description}
        </div>

        <div style="font-size: 0.45rem; line-height: 1.25; margin-right: 72px; margin-left: 0.25rem;">
            ${item.attribute || "\u00A0"}
        </div>

        <div style="
            display: flex;
            flex-direction: column;
            right: 5px;
            top: 10px;
            align-items: center;
            position: absolute;
            --tw-translate-y: -50%;
            transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));">

            <img src="${item.qrCode}" style="
                width: 50px;
                height: 50px;
                margin-bottom: 0.25rem;" />

            <div style="
                font-size: 8.5px;
                --tw-text-opacity: 1;
                color: rgb(0 0 0 / var(--tw-text-opacity));
                text-align: center;">
                ${item.code}
            </div>
        </div>
    </div>`;
