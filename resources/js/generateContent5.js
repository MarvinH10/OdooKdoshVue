export const generateContent5 = (item, style) => `
    <div style="
        width: 13rem;
        height: 14rem;
        padding: 0.25rem;
        text-align: center;
        position: relative;
        background-color: white;
        // border: 1px solid #ccc;
        color: black;
        font-family: Figtree, ui-sans-serif, system-ui, sans-serif,
            'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';">

        <div style="font-size: 1.875rem; line-height: 2.25rem; font-weight: 500; margin-bottom: 0.25rem;">
            S/ ${item.price}
        </div>

        <div style="font-size: 0.75rem; line-height: 1rem; margin-bottom: 0.25rem;">
            ${item.categ_id}
        </div>

        <div style="
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;">

        <div style="
            position: absolute;
            left: 32px;
            top: 50%;
            transform: translateY(-50%) translateX(-100%) rotate(-90deg);
            font-size: 0.875rem;
            line-height: 1.25rem;
            font-weight: 600;
            max-width: 100px;
            white-space: normal;
            word-wrap: break-word;">
            ${item.attribute}
        </div>

            <img src="${item.qrCode}" style="
                width: 100px;
                height: 100px;
                margin-left: 1rem;
                margin-right: 1rem;
                max-width: 100%;" />

        <div style="
            position: absolute;
            left: 200px;
            top: 50%;
            transform: translateY(-50%) translateX(-100%) rotate(-90deg);
            font-size: 0.875rem;
            line-height: 1.25rem;
            font-weight: 600;
            max-width: 100px;
            white-space: normal;
            word-wrap: break-word;">
            ${item.default_code}
        </div>
        </div>

        <div style="font-size: 0.75rem; line-height: 1rem; margin-top: 0.25rem; font-weight: 500;">
            ${item.code}
        </div>

        <div style="font-size: 0.75rem; line-height: 1rem;">
            ${item.description}
        </div>
    </div>`;
