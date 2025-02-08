export const generateContent1 = (item, style) => `
    <div style="
        width: 15rem;
        height: 15rem;
        padding: 0.25rem;
        text-align: center;
        position: relative;
        background-color: white;
        // border: 1px solid #ccc;
        color: black;
        font-family: Figtree, ui-sans-serif, system-ui, sans-serif,
            'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';">

        <div style="font-size: 2.25rem; line-height: 2.5rem; font-weight: 500; margin-bottom: 0.5rem;">
            S/ ${item.price}
        </div>

        <div style="font-size: 0.75rem; line-height: 1rem; margin-bottom: 1rem;">
            ${item.categ_id}
        </div>

        <div style="display: flex; align-items: center; justify-content: center; position: relative;">

            <div style="
                position: absolute;
                left: 50px;
                top: 50%;
                transform: translateY(-50%) translateX(-50%) rotate(-90deg);
                font-size: 0.75rem;
                font-weight: 600;
                white-space: normal;
                word-break: break-word;
                text-align: center;
                max-width: 100px;
                overflow: hidden;
                text-overflow: ellipsis;">
                ${item.attribute}
            </div>

            <img src="${item.qrCode}" style="
                width: ${style.qrCodeSize}px;
                height: ${style.qrCodeSize}px;
                margin: 0 auto;" />

            <div style="
                position: absolute;
                left: 160px;
                top: 50%;
                transform: translateY(-50%) rotate(-90deg);
                font-size: 0.875rem;
                font-weight: 600;
                white-space: normal;
                word-break: break-word;
                text-align: center;
                max-width: 100px;
                overflow: hidden;
                text-overflow: ellipsis;">
                ${item.default_code}
            </div>
        </div>

        <div style="font-size: 0.75rem; line-height: 1rem; margin-top: 0.5rem; font-weight: 500;">
            ${item.code}
        </div>

        <div style="font-size: 0.75rem; line-height: 1rem;">
            ${item.description}
        </div>
    </div>`;
