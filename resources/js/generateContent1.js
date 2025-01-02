export const generateContent1 = (item, style) => `
    <div style="
        width: 15rem;
        height: 15rem;
        margin-bottom: 20px;
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

        <div style="
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin-bottom: 10px;
            position: relative;">

        <div style="
            position: absolute;
            left: -20px;
            top: 50%;
            transform: translateY(-50%) rotate(-90deg);
            font-size: 0.875rem;
            line-height: 1.25rem;
            font-weight: 600;
            max-width: 100px;
            white-space: normal;
            word-wrap: break-word;">
            ${item.attribute}
        </div>

            <img src="${item.qrCode}" style="
                width: ${style.qrCodeSize}px;
                height: ${style.qrCodeSize}px;" />
        </div>

        <div style="font-size: 0.75rem; line-height: 1rem; margin-top: 0.5rem; font-weight: 500;">
            ${item.code}
        </div>

        <div style="font-size: 0.75rem; line-height: 1rem;">
            ${item.description}
        </div>
    </div>`;
