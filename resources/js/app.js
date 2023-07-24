/* eslint-disable vue/multi-word-component-names */
/* eslint-disable vue/no-reserved-component-names */
require('./bootstrap')

import { createApp, h, reactive } from 'vue'
import { createInertiaApp, Link, Head } from '@inertiajs/inertia-vue3'
import { InertiaProgress } from '@inertiajs/progress'
import { i18nVue } from 'laravel-vue-i18n'
import PrimeVue from 'primevue/config'

import router from './router'

import AutoComplete from 'primevue/autocomplete'
//import Accordion from 'primevue/accordion';
//import AccordionTab from 'primevue/accordiontab';
//import Avatar from 'primevue/avatar';
//import AvatarGroup from 'primevue/avatargroup';
//import Badge from 'primevue/badge';
import BadgeDirective from 'primevue/badgedirective'
import Button from 'primevue/button'
import Breadcrumb from 'primevue/breadcrumb'
//import Calendar from 'primevue/calendar';
import Card from 'primevue/card'
//import CascadeSelect from 'primevue/cascadeselect';
//import Carousel from 'primevue/carousel';
//import Chart from 'primevue/chart';
import Checkbox from 'primevue/checkbox'
//import Chip from 'primevue/chip';
//import Chips from 'primevue/chips';
//import ColorPicker from 'primevue/colorpicker';
//import Column from 'primevue/column';
import ConfirmDialog from 'primevue/confirmdialog'
//import ConfirmPopup from 'primevue/confirmpopup';
import ConfirmationService from 'primevue/confirmationservice'
//import ContextMenu from 'primevue/contextmenu';
//import DataTable from 'primevue/datatable';
//import DataView from 'primevue/dataview';
//import DataViewLayoutOptions from 'primevue/dataviewlayoutoptions';
import Dialog from 'primevue/dialog'
//import Divider from 'primevue/divider';
//import Dropdown from 'primevue/dropdown';
//import Fieldset from 'primevue/fieldset';
//import FileUpload from 'primevue/fileupload';
//import FullCalendar from 'primevue/fullcalendar';
//import Galleria from 'primevue/galleria';
//import Image from 'primevue/image';
//import InlineMessage from 'primevue/inlinemessage';
//import Inplace from 'primevue/inplace';
//import InputSwitch from 'primevue/inputswitch';
import InputText from 'primevue/inputtext'
//import InputMask from 'primevue/inputmask';
//import InputNumber from 'primevue/inputnumber';
//import Knob from 'primevue/knob';
//import Listbox from 'primevue/listbox';
import Menu from 'primevue/menu'
//import Menubar from 'primevue/menubar';
import Message from 'primevue/message'
//import MultiSelect from 'primevue/multiselect';
//import OrderList from 'primevue/orderlist';
//import OrganizationChart from 'primevue/organizationchart';
//import OverlayPanel from 'primevue/overlaypanel';
//import Paginator from 'primevue/paginator';
//import Panel from 'primevue/panel';
//import PanelMenu from 'primevue/panelmenu';
//import Password from 'primevue/password';
//import PickList from 'primevue/picklist';
//import ProgressBar from 'primevue/progressbar';
//import Rating from 'primevue/rating';
//import RadioButton from 'primevue/radiobutton';
import Ripple from 'primevue/ripple'
//import SelectButton from 'primevue/selectbutton';
//import ScrollPanel from 'primevue/scrollpanel';
import ScrollTop from 'primevue/scrolltop'
//import Skeleton from 'primevue/skeleton';
//import Slider from 'primevue/slider';
import Sidebar from 'primevue/sidebar'
//import SpeedDial from 'primevue/speeddial';
//import SplitButton from 'primevue/splitbutton';
//import Splitter from 'primevue/splitter';
//import SplitterPanel from 'primevue/splitterpanel';
//import Steps from 'primevue/steps';
import StyleClass from 'primevue/styleclass'
//import FocusTrap from 'primevue/focustrap';
//import TabMenu from 'primevue/tabmenu';
//import TieredMenu from 'primevue/tieredmenu';
import Textarea from 'primevue/textarea'
import Toast from 'primevue/toast'
import ToastService from 'primevue/toastservice'
//import Toolbar from 'primevue/toolbar';
//import TabView from 'primevue/tabview';
//import TabPanel from 'primevue/tabpanel';
//import Tag from 'primevue/tag';
//import Timeline from 'primevue/timeline';
//import ToggleButton from 'primevue/togglebutton';
import Tooltip from 'primevue/tooltip'
//import Tree from 'primevue/tree';
//import TreeSelect from 'primevue/treeselect';
//import TreeTable from 'primevue/treetable';
//import TriStateCheckbox from 'primevue/tristatecheckbox';
//import CodeHighlight from './AppCodeHighlight';
//import BlockViewer from './Layouts/BlockViewer';

import '@fortawesome/fontawesome-free/css/all.min.css'
import 'primevue/resources/primevue.min.css'
import 'primeicons/primeicons.css'
import 'primeflex/primeflex.css'
import 'prismjs/themes/prism-coy.css'
import '../css/flags/flags.css'
import { Inertia } from '@inertiajs/inertia'

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Soapp'

const html_lang = window.document.getElementsByTagName('html')[0].getAttribute('lang')
const appLang = (html_lang?html_lang.replace('-', '_'):'pt_PT')
let primevue_locale = require('./primevue_locales/locale_' + appLang)
const locale = primevue_locale.locale

//Impedir o utilizador de voltar atrás no browser (history back) depois de ter feito logout
Inertia.on('success', (event) => {
    let isAthenticated = event.detail.page.props.user !== null
    window.localStorage.setItem('isAthenticated', isAthenticated)
})
window.addEventListener('popstate', (event) => {
    if(window.localStorage.getItem('isAthenticated') === 'false'){
        event.stopImmediatePropagation()
        //Inertia.replace('/login');//sem refresh
        window.location.replace('/login')//Com refresh
    }
})

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => require(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {
        const the_app = createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(router)
            .use(PrimeVue, {
                ripple: true,
                inputStyle: 'outlined',
                locale //Traduções da biblioteca primevue
            })
            .use(i18nVue, {
                resolve: lang => require(`../../lang/${lang}.json`),
            })
            .use(ToastService)
            .use(ConfirmationService)
            .mixin({ methods: { 
                route,
            } })
            .provide('appLocale', appLang)
            .provide('i18nVue', {
                resolve: lang => require(`../../lang/${lang}.json`),
            })

        the_app.config.globalProperties.$appState = reactive({ RTL: false, isNewThemeLoaded: false, layoutMode: 'light', appLocale: appLang })

        the_app.directive('tooltip', Tooltip)
        the_app.directive('badge', BadgeDirective)
        the_app.directive('ripple', Ripple)
        //the_app.directive('code', CodeHighlight);
        the_app.directive('styleclass', StyleClass)
        //the_app.directive('focustrap', FocusTrap);

        // eslint-disable-next-line vue/multi-word-component-names
        the_app.component('Head', Head)
        the_app.component('Link', Link)
        the_app.component('AutoComplete', AutoComplete)
        the_app.component('Breadcrumb', Breadcrumb)
        the_app.component('Button', Button)
        the_app.component('ConfirmDialog', ConfirmDialog)
        the_app.component('Dialog', Dialog)
        the_app.component('InputText', InputText)
        the_app.component('Checkbox', Checkbox)
        the_app.component('Textarea', Textarea)
        the_app.component('Menu', Menu)
        the_app.component('Message', Message)
        the_app.component('ScrollTop', ScrollTop)
        the_app.component('Sidebar', Sidebar)
        the_app.component('Toast', Toast)
        the_app.component('Card', Card)

        the_app.mount(el)

        document.getElementById('app_loader').remove()

    },
})

InertiaProgress.init({ color: '#fbc02d' })//#4B5563

