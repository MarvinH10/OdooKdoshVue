export const generateContent2 = (item, style) => `
    <div style="
        width: 12.5rem;
        height: 5.5rem;
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

        <div style="font-size: 0.5rem; line-height: 1.25; margin-right: 80px; margin-left: 0.25rem;">
            ${item.categ_id}
        </div>

        <div style="font-size: 0.5rem; line-height: 1.25; margin-right: 80px; margin-left: 0.25rem;">
            ${item.default_code || "\u00A0"}
        </div>

        <div style="font-size: 0.5rem; line-height: 1.25; margin-right: 80px; margin-left: 0.25rem;">
            ${item.description}
        </div>

        <div style="font-size: 0.5rem; line-height: 1.25; margin-right: 80px; margin-left: 0.25rem;">
            ${item.attribute}
        </div>

        <div style="
            display: flex;
            left: 60px;
            top: -72px;
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
                left: 127px;
                transform: translateY(-50%) translateX(-100%);
            ">
                ${item.code}
            </div>
        </div>
    </div>`;
