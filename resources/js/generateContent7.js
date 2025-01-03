export const generateContent7 = (item, style) => `
    <div style="
        border-radius: 50%;
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

        <div style="font-size: 2.25rem; line-height: 2.5rem; font-weight: 500; margin-top: 0.95rem;">
            S/ ${item.price}
        </div>

        <div style="font-size: 0.60rem; margin-bottom: 0.5rem;">
            ${item.categ_id}
        </div>

        <div style="
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;">

        <div style="
            position: absolute;
            left: 5.5rem;
            top: 50%;
            transform: translateY(-50%) translateX(-100%) rotate(-90deg);
            font-size: 0.75rem;
            line-height: 1rem;
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
        </div>

        <div style="font-size: 0.75rem; line-height: 1rem; margin-top: 0.25rem; font-weight: 500;">
            ${item.code}
        </div>

        <div style="font-size: 0.75rem; line-height: 1rem;">
            ${item.description}
        </div>
    </div>`;
