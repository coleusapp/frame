import { genesisIcons } from '@formkit/icons';
import { defaultConfig } from '@formkit/vue';
import { rootClasses } from '../support/formkit.theme';
import { inputs, proPlugin } from '../support/formkit.config';

export default defaultConfig({
    plugins: [proPlugin],
    inputs,
    icons: {
        ...genesisIcons
    },
    config: {
        rootClasses,
    }
});
