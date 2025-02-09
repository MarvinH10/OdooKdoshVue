export const generateContent4 = (item, style) => `
    <div style="
        width: 400px;
        height: 400px;
        padding: 0.25rem;
        text-align: center;
        position: relative;
        background-color: white;
        // border: 1px solid #ccc;
        color: black;
        font-family: Figtree, ui-sans-serif, system-ui, sans-serif,
            'Apple Color Emoji', 'Segoe UI Emoji', 'Segoe UI Symbol', 'Noto Color Emoji';">

        <div style="
            display: flex;
            justify-content: center;">
            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="24%" viewBox="0 0 432 432"
            enable-background="new 0 0 432 432">
            <path fill="#000000" opacity="1.000000" stroke="none" d="
            M180.382690,304.727081 C164.485611,293.230438 157.848129,277.007538 154.978775,258.159760 C151.277405,269.091064 148.081787,279.360748 160.548508,287.272064 C160.022079,287.680634 159.495667,288.089172 158.969254,288.497742 C151.753784,286.457947 144.448227,284.682526 137.360825,282.266937 C133.810394,281.056885 137.249725,277.822815 136.415619,275.586639 C135.581757,274.585266 134.614838,275.226074 133.734634,275.550476 C115.535217,282.257904 96.353584,269.039825 96.260735,249.346527 C96.184151,233.105286 107.394981,215.111542 126.347755,213.170486 C135.389374,212.244476 143.482574,214.559387 150.705902,221.189056 C153.573273,213.795013 154.982742,206.491302 156.851669,199.336990 C160.130020,186.787338 163.138489,174.165863 166.530212,161.647705 C167.991425,156.254608 166.910446,152.196533 162.108170,149.206635 C160.888092,148.447021 159.147720,147.685333 160.165817,145.787155 C160.989655,144.251160 162.469360,144.982513 163.736298,145.322952 C168.557587,146.618500 173.343918,148.073090 178.213654,149.150131 C181.869095,149.958588 182.623978,151.691940 181.669098,155.224899 C177.372437,171.122238 173.288742,187.077194 169.139297,203.014252 C168.600815,205.082489 168.126495,207.167435 167.884781,209.612976 C181.108994,191.872162 199.293732,185.108322 220.522537,185.723953 C241.590729,186.334961 259.996277,193.055099 273.384674,211.570862 C277.303528,202.982986 283.723389,198.247711 292.321289,196.282547 C295.802582,195.486832 299.280853,195.779633 302.764099,195.653976 C309.237030,195.420486 310.405640,194.046844 309.400604,187.685226 C309.178986,186.282440 308.054657,184.516464 310.135651,183.850281 C312.556793,183.075256 312.753845,185.365555 313.174896,186.834473 C315.694824,195.625626 318.123322,204.443008 320.568634,213.255447 C320.950867,214.632797 321.965576,216.257370 319.926025,217.110870 C318.104462,217.873108 317.296631,216.393021 316.393768,215.149658 C314.635590,212.728424 312.967133,210.205688 310.941315,208.022064 C305.294128,201.934952 296.973450,200.300491 289.469208,203.622604 C285.027405,205.588974 281.755920,208.954788 281.700470,213.792633 C281.642853,218.814163 284.771301,222.447006 289.469238,224.465775 C295.356201,226.995453 301.699585,227.605698 307.919952,228.597519 C321.625183,230.782761 324.712341,232.061646 334.802704,240.871628 C337.018158,236.668320 334.790924,233.066818 333.380005,229.609756 C328.157837,216.814529 322.693604,204.118134 317.325012,191.382629 C312.021820,178.802109 306.752502,166.207199 301.404053,153.645950 C300.100830,150.585297 298.711365,147.537598 297.068085,144.651016 C294.575165,140.271988 291.051758,138.020538 285.838654,140.139755 C283.198517,141.213043 281.273254,140.924866 281.148590,136.869080 C287.232513,134.251175 293.451477,131.521957 299.716095,128.901993 C302.844208,127.593781 303.350586,130.285660 304.156891,132.174133 C309.257019,144.119339 314.288422,156.093887 319.365814,168.048843 C320.187012,169.982285 321.124542,171.866302 322.293396,174.388245 C323.462036,172.386749 324.211517,171.118942 324.946167,169.842590 C333.911041,154.267578 353.968231,153.455643 364.195465,168.337509 C370.961090,178.182297 374.251923,189.691681 379.204681,200.406158 C380.391388,202.973450 381.509094,205.574020 382.749878,208.114685 C384.900177,212.517685 388.004608,214.931244 393.196350,213.061234 C394.576752,212.564026 396.363831,212.034058 396.980225,214.053802 C397.599243,216.082413 395.652924,216.526291 394.378845,217.087616 C385.692963,220.914322 376.976105,224.671082 368.256805,228.421356 C367.058136,228.936905 365.641388,229.705063 364.743561,228.071487 C363.688354,226.151596 365.324463,225.501770 366.582001,224.669647 C373.320099,220.211273 373.802032,219.014542 370.776733,211.599182 C366.500031,201.116684 362.020081,190.717117 357.622101,180.284180 C357.428558,179.825073 357.210846,179.372391 356.962738,178.940659 C354.172577,174.085541 351.264557,169.086838 344.699066,169.430511 C337.864166,169.788300 333.562592,173.922073 330.731720,179.966660 C328.420929,184.900681 328.009949,189.413605 330.484955,194.673065 C335.656830,205.663361 340.017090,217.032516 344.903503,228.161407 C347.793213,234.742767 349.556091,235.430008 356.610260,233.855179 C357.998932,233.545166 359.702454,232.575226 360.558167,234.513748 C361.630737,236.943466 359.306030,237.243500 357.952576,237.852722 C352.335052,240.381317 346.663788,242.791061 341.001312,245.218933 C338.914062,246.113876 337.455597,247.165588 337.262543,249.872574 C335.839111,269.830566 325.089508,279.861542 304.895386,280.309387 C304.562195,280.316772 304.228912,280.316193 303.895660,280.320496 C294.721649,280.438599 294.721649,280.438629 294.760834,289.257538 C291.518188,289.687042 291.069641,287.237305 290.436005,285.059998 C288.067719,276.922272 285.730957,268.775360 283.078766,259.579132 C280.970367,266.401428 279.376465,272.355469 277.077850,278.083435 C267.835663,301.114014 250.524170,312.290558 226.161316,314.488800 C209.825668,315.962799 194.684448,313.638947 180.382690,304.727081
            M240.573929,295.091614 C262.042847,276.082520 263.252472,232.861069 246.829407,210.701889 C231.608459,190.164688 200.068939,189.308685 187.425995,215.444199 C178.613037,233.662354 178.296707,252.605820 183.134506,271.856476 C185.737259,282.213440 190.185455,291.701355 198.748566,298.513184 C210.401688,307.783051 226.011230,306.522461 240.573929,295.091614
            M110.186310,253.224304 C112.080551,261.490845 117.005547,267.124512 124.980515,269.977905 C131.147583,272.184448 138.366058,269.661163 139.704956,264.807800 C142.041473,256.338226 144.450836,247.877014 146.392990,239.313278 C148.845184,228.500519 143.223740,219.594498 133.348587,218.626663 C127.084343,218.012726 121.607063,220.511520 117.093651,224.818054 C109.033012,232.509201 108.082680,242.120361 110.186310,253.224304
            M317.577118,248.811050 C306.828644,244.209351 294.936951,244.202896 284.003510,239.944229 C281.280090,249.811203 285.656403,257.140442 291.148987,263.875671 C296.504120,270.442352 303.634308,273.525513 312.256592,272.808380 C318.915771,272.254486 323.902557,268.621490 325.344116,263.113068 C326.697784,257.940460 324.126678,252.894714 317.577118,248.811050
            z"></path>
            <path fill="#000000" opacity="1.000000" stroke="none" d="
            M139.404572,128.794281 C145.378677,135.777679 151.130280,142.508072 156.834747,149.183334 C155.066544,150.859406 154.243301,150.268478 153.517776,149.520905 C149.569016,145.452011 145.944794,145.978333 141.855286,149.658569 C124.519379,165.259537 107.027794,180.687454 89.606514,196.193695 C88.749748,196.956284 88.019447,197.860962 86.770752,199.187714 C97.431915,202.261765 107.697647,204.756241 118.405914,203.337051 C124.241798,202.563629 125.175552,199.355255 121.565071,194.700104 C120.230385,192.979218 117.859520,191.787491 118.775803,188.570221 C126.488098,195.141052 132.687485,203.035522 139.139572,210.930298 C138.075714,211.605621 137.673080,211.990967 137.554901,211.917664 C130.785324,207.719208 123.671143,208.816315 116.350182,210.050400 C110.772987,210.990555 105.075378,210.413086 99.453247,209.390533 C97.728615,209.076859 95.894905,208.385300 93.229660,209.408981 C93.749359,228.230164 86.920280,247.424255 94.662064,266.796692 C85.964523,259.172180 78.870796,250.336533 70.666656,240.754517 C80.664505,241.887604 80.754051,241.888031 80.799316,232.312088 C80.843010,223.069199 82.486107,213.840668 81.212532,203.512192 C74.843826,209.270203 69.045822,214.437439 63.341175,219.705750 C60.976978,221.889114 61.469761,224.400818 62.845966,226.992676 C63.605488,228.423126 65.615685,229.607651 63.688984,232.856339 C56.652740,224.749176 50.003849,217.088318 43.402901,209.482712 C45.007336,207.859558 45.718071,208.232864 46.415417,208.758499 C52.613155,213.430023 53.165863,213.573181 59.116486,208.297714 C84.303474,185.968445 109.425201,163.565292 134.510254,141.121506 C139.639755,136.532120 139.607010,135.690140 136.123856,129.688507 C135.732971,129.015015 135.552277,128.219528 135.291122,127.525818 C137.354004,125.905106 138.128235,127.660408 139.404572,128.794281
            z"></path>
        </svg>
        </div>

        <div style="
            font-size: 2.25rem;
            line-height: 2.5rem;
            font-weight: 500;
            margin-bottom: 0.5rem;">
            S/ ${item.price}
        </div>

        <div style="
            font-size: 0.875rem;
            line-height: 1.25rem;">
            ${item.description}
        </div>

        <div style="
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;">

            <div style="
                position: absolute;
                left: 130px;
                top: 40px;
                transform: translateY(-50%) translateX(-100%) rotate(-90deg);
                font-size: 0.875rem;
                line-height: 1.25rem;
                font-weight: 600;
                word-wrap: break-word;">
                ${item.categ_id}
            </div>

            <img src="${item.qrCode || ""}" alt="QR Code" style="
                width: ${style.qrCodeSize}px;
                height: ${style.qrCodeSize}px;
                margin-left: 1rem;
                margin-right: 1rem;
                max-width: 100%;
                display: block;
                vertical-align: middle;" />

            <div style="
                position: absolute;
                left: 390px;
                top: 40px;
                transform: translateY(-50%) translateX(-100%) rotate(-90deg);
                font-size: 0.875rem;
                line-height: 1.25rem;
                font-weight: 600;
                word-wrap: break-word;">
                ${item.default_code}
            </div>
        </div>

        <div style="
            font-size: 0.75rem;
            line-height: 1rem;
            margin-top: 0.5rem;">
            ${item.code}
        </div>

        <div style="
            font-size: 17px;
            line-height: 1.5rem;
            font-weight: 500;
            display: flex;
            justify-content: space-between;
            margin-top: 65px;">
            <span>MCMLXXXIX</span>
            <span>MCMLXXXIX</span>
            <span>MCMLXXXIX</span>
        </div>
    </div>`;
