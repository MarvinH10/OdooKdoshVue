export const generateContent6 = (item, style) => `
    <div style="
        width: 230px;
        height: 80px;
        text-align: center;
        position: relative;
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

        <div style="font-size: 0.5rem; line-height: 1.25; margin-right: 72px;">
            ${item.default_code || "\u00A0"}
        </div>

        <div style="font-size: 0.5rem; line-height: 1.25; margin-right: 72px;">
            ${item.description}
        </div>

        <div style="font-size: 7.8px; line-height: 1.25; margin-right: 72px; margin-left: 0.25rem;">
            ${item.attribute || "\u00A0"}
        </div>

        <div style="
            display: flex;
            left: 80px;
            top: -65px;
            justify-content: center;
            align-items: center;
            position: relative;">

            <img src="${item.qrCode}" style="
                width: 55px;
                height: 55px;
                margin-left: 1rem;
                margin-right: 1rem;
                max-width: 100%;" />

            <div style="
                position: absolute;
                font-size: 8.5px;
                color: black;
                top: 65px;
                left: 146px;
                transform: translateY(-50%) translateX(-100%);
            ">
                ${item.code}
            </div>
        </div>
    </div>`;
