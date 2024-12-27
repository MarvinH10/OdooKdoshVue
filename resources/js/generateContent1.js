export const generateContent1 = (item, style) => `
    <div style="
        width: ${style.width};
        height: ${style.height};
        margin-bottom: 20px;
        padding: 10px;
        text-align: center;
        position: relative;
        background-color: white;
        border: 1px solid #ccc;">
        <div style="font-size: ${style.priceFontSize}; font-weight: bold; margin-bottom: 0.5rem;">
            S/ ${item.price}
        </div>
        <div style="color: black; margin-bottom: 1rem;">
            ${item.categ_id}
        </div>
        <div style="
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 10px;">
            <div style="transform: rotate(-90deg); margin-right: 10px; font-size: 10px; font-weight: bold;">
                ${item.attribute}
            </div>
            <img src="${item.qrCode}" style="
                width: ${style.qrCodeSize}px;
                height: ${style.qrCodeSize}px;" />
        </div>
        <div style="color: #333; margin-bottom: 5px;">
            ${item.description}
        </div>
        <div style="color: #999; font-size: 12px;">
            ${item.code}
        </div>
    </div>`;
