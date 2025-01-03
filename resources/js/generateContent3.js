export const generateContent3 = (group, style) => `
    <div style="
        width: 12.5rem;
        height: 5.5rem;
        text-align: center;
        position: relative;
        background-color: white;
        // border: 1px solid #ccc;
        color: black;
        font-family: Figtree, ui-sans-serif, system-ui, sans-serif,
            'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';">
        <div style="
            justify-content: space-around;
            align-items: center;
            height: 100%;
            display: flex;">
            ${group
                .map(
                    (item) => `
                        <div style="position: relative; text-align: center;">
                            <div style="font-size: 0.75rem; line-height:1rem; margin-bottom: 0.25rem;">
                                S/ ${item.price || 'N/A'}
                            </div>
                            <img src="${item.qrCode || ''}" alt="QR Code" style="
                                width: ${style.qrCodeSize}px;
                                height: ${style.qrCodeSize}px;
                                margin: 0 8px;
                            " />
                            <div style="color: black; font-size: 7.5px;">
                                ${item.code || 'N/A'}
                            </div>
                        </div>
                    `
                )
                .join('')}
        </div>
    </div>`;
