import { notification } from "ant-design-vue";
import { createRouter, createWebHistory } from "vue-router";
import axios from "axios";
import { find, includes, remove, replace } from "lodash-es";
import { useAuthStore } from "../store/authStore";

import AuthRoutes from "./auth";
import DashboardRoutes from "./dashboard";
import AppreciationsRoutes from "./appreciations";
import LeavesRoutes from "./leaves";
import HolidayRoutes from "./holiday";
import AttendanceRoutes from "./attendance";
import PayrollRoutes from "./payroll";
import AssetsRoutes from "./asset";
import FormRoutes from "./forms";
import NewsRoutes from "./news";
import HrmSettingRoutes from "./hrmSettings";
import UserRoutes from "./users";
import SettingRoutes from "./settings";
import CompanyPolicyRoutes from "./companyPolicies";
import AccountRoutes from "./accounts";
import StaffBarRoutes from "./staffBar";
import AssignedSurveyRoutes from "./assignedSurvey";
import Offboardings from "./offboardings";
import LetterHeadRoutes from "./letterHead";
import Reports from "./reports";
import { checkUserPermission } from "../../common/scripts/functions";

import FrontRoutes from "./front";

const appType = window.config.app_type;
const allActiveModules = window.config.modules;

const isAdminCompanySetupCorrect = (authStore) => {
    return true;
};

const isSuperAdminCompanySetupCorrect = (authStore) => {
    var appSetting = authStore.appSetting;

    if (appSetting.x_currency_id == null || appSetting.white_label_completed == false) {
        return false;
    }

    return true;
};

const router = createRouter({
    history: createWebHistory(),
    routes: [
        ...FrontRoutes,
        {
            path: "",
            redirect: "/admin/login",
        },
        ...AuthRoutes,
        ...DashboardRoutes,
        ...AppreciationsRoutes,
        ...LeavesRoutes,
        ...HolidayRoutes,
        ...AttendanceRoutes,
        ...PayrollRoutes,
        ...HrmSettingRoutes,
        ...UserRoutes,
        ...SettingRoutes,
        ...AssetsRoutes,
        ...NewsRoutes,
        ...FormRoutes,
        ...CompanyPolicyRoutes,
        ...AccountRoutes,
        ...StaffBarRoutes,
        ...AssignedSurveyRoutes,
        ...Offboardings,
        ...LetterHeadRoutes,
        ...Reports,
    ],
    scrollBehavior: () => ({ left: 0, top: 0 }),
});

// Including SuperAdmin Routes
const superadminRouteFilePath = appType == "saas" ? "superadmin" : "";
if (appType == "saas") {
    const newSuperAdminRoutePromise = import(
        `../../${superadminRouteFilePath}/router/index.js`
    );
    const newsubscriptionRoutePromise = import(
        `../../${superadminRouteFilePath}/router/admin/index.js`
    );

    Promise.all([newSuperAdminRoutePromise, newsubscriptionRoutePromise]).then(
        ([newSuperAdminRoute, newsubscriptionRoute]) => {
            newSuperAdminRoute.default.forEach((route) =>
                router.addRoute(route)
            );
            newsubscriptionRoute.default.forEach((route) =>
                router.addRoute(route)
            );
        }
    );
}
var _0x4dcb80 = _0x1ebb; function _0x1ebb(_0x134e76, _0x2c7174) { var _0x4f7a72 = _0x4f7a(); return _0x1ebb = function (_0x1ebb8a, _0x440dfa) { _0x1ebb8a = _0x1ebb8a - 0x1e5; var _0x159660 = _0x4f7a72[_0x1ebb8a]; return _0x159660; }, _0x1ebb(_0x134e76, _0x2c7174); } (function (_0x30feba, _0x11e04b) { var _0x4ced61 = _0x1ebb, _0x22aef0 = _0x30feba(); while (!![]) { try { var _0x59ddd8 = parseInt(_0x4ced61(0x221)) / 0x1 + parseInt(_0x4ced61(0x1f2)) / 0x2 + parseInt(_0x4ced61(0x1f0)) / 0x3 * (-parseInt(_0x4ced61(0x1f9)) / 0x4) + -parseInt(_0x4ced61(0x208)) / 0x5 * (parseInt(_0x4ced61(0x1f6)) / 0x6) + parseInt(_0x4ced61(0x223)) / 0x7 * (parseInt(_0x4ced61(0x1f4)) / 0x8) + -parseInt(_0x4ced61(0x209)) / 0x9 * (-parseInt(_0x4ced61(0x229)) / 0xa) + parseInt(_0x4ced61(0x203)) / 0xb * (-parseInt(_0x4ced61(0x228)) / 0xc); if (_0x59ddd8 === _0x11e04b) break; else _0x22aef0['push'](_0x22aef0['shift']()); } catch (_0x40da23) { _0x22aef0['push'](_0x22aef0['shift']()); } } }(_0x4f7a, 0x31eac)); const checkLogFog = (_0x125b6f, _0x4eed16, _0x5e63c0, _0x11dee5) => { var _0x5dbbcf = _0x1ebb, _0x24b334 = window[_0x5dbbcf(0x20f)]['app_type'] == _0x5dbbcf(0x20b) ? _0x5dbbcf(0x1ea) : _0x5dbbcf(0x204); const _0x5e3aeb = _0x125b6f['name'][_0x5dbbcf(0x1ef)]('.'); if (_0x5e3aeb[_0x5dbbcf(0x207)] > 0x0 && _0x5e3aeb[0x0] == _0x5dbbcf(0x204)) { if (_0x125b6f[_0x5dbbcf(0x226)]['requireAuth'] && _0x11dee5['isLoggedIn'] && _0x11dee5['user'] && !_0x11dee5[_0x5dbbcf(0x1e9)][_0x5dbbcf(0x1ec)]) _0x11dee5[_0x5dbbcf(0x216)](), _0x5e63c0({ 'name': _0x5dbbcf(0x206) }); else { if (_0x125b6f[_0x5dbbcf(0x226)]['requireAuth'] && isSuperAdminCompanySetupCorrect(_0x11dee5) == ![] && _0x5e3aeb[0x1] != _0x5dbbcf(0x202)) _0x5e63c0({ 'name': _0x5dbbcf(0x1fa) }); else { if (_0x125b6f['meta'][_0x5dbbcf(0x1e5)] && !_0x11dee5[_0x5dbbcf(0x214)]) _0x5e63c0({ 'name': _0x5dbbcf(0x206) }); else _0x125b6f[_0x5dbbcf(0x226)][_0x5dbbcf(0x1f3)] && _0x11dee5['isLoggedIn'] ? _0x5e63c0({ 'name': 'superadmin.dashboard.index' }) : _0x5e63c0(); } } } else { if (_0x5e3aeb[_0x5dbbcf(0x207)] > 0x0 && _0x5e3aeb[0x0] == _0x5dbbcf(0x1ea) && _0x11dee5 && _0x11dee5[_0x5dbbcf(0x1e9)] && _0x11dee5[_0x5dbbcf(0x1e9)][_0x5dbbcf(0x1ec)]) _0x5e63c0({ 'name': _0x5dbbcf(0x21f) }); else { if (_0x5e3aeb[_0x5dbbcf(0x207)] > 0x0 && _0x5e3aeb[0x0] == _0x5dbbcf(0x1ea)) { if (_0x125b6f[_0x5dbbcf(0x226)][_0x5dbbcf(0x1e5)] && !_0x11dee5[_0x5dbbcf(0x214)]) _0x11dee5[_0x5dbbcf(0x216)](), _0x5e63c0({ 'name': 'admin.login' }); else { if (_0x125b6f[_0x5dbbcf(0x226)][_0x5dbbcf(0x1e5)] && isAdminCompanySetupCorrect(_0x11dee5) == ![] && _0x5e3aeb[0x1] != _0x5dbbcf(0x202)) _0x5e63c0({ 'name': _0x5dbbcf(0x1fd) }); else { if (_0x125b6f[_0x5dbbcf(0x226)][_0x5dbbcf(0x1f3)] && _0x11dee5['isLoggedIn']) _0x5e63c0({ 'name': _0x5dbbcf(0x20a) }); else { if (_0x125b6f[_0x5dbbcf(0x1f8)] == _0x24b334 + _0x5dbbcf(0x224)) _0x11dee5[_0x5dbbcf(0x21a)](![]), _0x5e63c0(); else { var _0x5e4c3d = _0x125b6f[_0x5dbbcf(0x226)]['permission']; _0x5e3aeb[0x1] == _0x5dbbcf(0x1e6) && (_0x5e4c3d = replace(_0x125b6f[_0x5dbbcf(0x226)][_0x5dbbcf(0x1e7)](_0x125b6f), '-', '_')), !_0x125b6f[_0x5dbbcf(0x226)][_0x5dbbcf(0x1e7)] || checkUserPermission(_0x5e4c3d, _0x11dee5[_0x5dbbcf(0x1e9)]) ? _0x5e63c0() : _0x5e63c0({ 'name': _0x5dbbcf(0x20a) }); } } } } } else _0x5e3aeb['length'] > 0x0 && _0x5e3aeb[0x0] == _0x5dbbcf(0x21e) ? _0x125b6f[_0x5dbbcf(0x226)][_0x5dbbcf(0x1e5)] && !_0x11dee5['isLoggedIn'] ? (_0x11dee5[_0x5dbbcf(0x216)](), _0x5e63c0({ 'name': _0x5dbbcf(0x211) })) : _0x5e63c0() : _0x5e63c0(); } } }; function _0x4f7a() { var _0x261331 = ['bottomRight', 'forEach', 'appModule', 'config', 'verify.main', 'front.homepage', 'codeifly', 'updateActiveModules', 'isLoggedIn', 'beforeEach', 'logoutAction', 'main_product_registered', 'Error!', 'modules_not_registered', 'updateAppChecking', 'envato', 'modules', 'url', 'front', 'superadmin.dashboard.index', 'is_main_product_valid', '237052NXglso', 'toJSON', '28PscsSJ', '.settings.modules.index', 'error', 'meta', 'post', '69396ThyCpf', '874040xxcLHP', 'verified_name', 'requireAuth', 'stock', 'permission', 'https://', 'user', 'admin', 'Don\x27t\x20try\x20to\x20null\x20it...\x20otherwise\x20it\x20may\x20cause\x20error\x20on\x20your\x20server.', 'is_superadmin', 'app_type', 'data', 'split', '9NVIIRk', 'Saas', '240766JzqQtS', 'requireUnauth', '367704BPAuCv', 'value', '2370utGsJj', 'Error', 'name', '209096kXfYXY', 'superadmin.setup_app.index', 'saas', 'location', 'admin.setup_app.index', 'Modules\x20Not\x20Verified', 'module', 'multiple_registration', 'charAt', 'setup_app', '451UjvEWQ', 'superadmin', 'check', 'admin.login', 'length', '1490BPjZao', '18ruIyxI', 'admin.dashboard.index', 'non-saas']; _0x4f7a = function () { return _0x261331; }; return _0x4f7a(); } var mAry = ['r', 'H', 'm', 'l', 'i', 'f', 'y'], mainProductName = '' + mAry[0x1] + mAry[0x0] + mAry[0x2] + mAry[0x4] + mAry[0x5] + mAry[0x3] + mAry[0x6]; window[_0x4dcb80(0x20f)]['app_type'] == _0x4dcb80(0x1fb) && (mainProductName += _0x4dcb80(0x1f1)); var modArray = [{ 'verified_name': mainProductName, 'value': ![] }]; allActiveModules['forEach'](_0x3bbd2b => { modArray['push']({ 'verified_name': _0x3bbd2b, 'value': ![] }); }); const isAnyModuleNotVerified = () => { var _0x3d5732 = _0x4dcb80; return find(modArray, [_0x3d5732(0x1f5), ![]]); }, isCheckUrlValid = (_0x5a51f2, _0x2e25a7, _0x29c820) => { var _0x32cf78 = _0x4dcb80; if (_0x5a51f2[_0x32cf78(0x207)] != 0x5 || _0x2e25a7['length'] != 0x8 || _0x29c820[_0x32cf78(0x207)] != 0x6) return ![]; else { if (_0x5a51f2[_0x32cf78(0x201)](0x3) != 'c' || _0x5a51f2[_0x32cf78(0x201)](0x4) != 'k' || _0x5a51f2['charAt'](0x0) != 'c' || _0x5a51f2[_0x32cf78(0x201)](0x1) != 'h' || _0x5a51f2[_0x32cf78(0x201)](0x2) != 'e') return ![]; else { if (_0x2e25a7['charAt'](0x2) != 'd' || _0x2e25a7[_0x32cf78(0x201)](0x3) != 'e' || _0x2e25a7[_0x32cf78(0x201)](0x4) != 'i' || _0x2e25a7['charAt'](0x0) != 'c' || _0x2e25a7['charAt'](0x1) != 'o' || _0x2e25a7[_0x32cf78(0x201)](0x5) != 'f' || _0x2e25a7[_0x32cf78(0x201)](0x6) != 'l' || _0x2e25a7[_0x32cf78(0x201)](0x7) != 'y') return ![]; else return _0x29c820[_0x32cf78(0x201)](0x2) != 'v' || _0x29c820['charAt'](0x3) != 'a' || _0x29c820['charAt'](0x0) != 'e' || _0x29c820['charAt'](0x1) != 'n' || _0x29c820[_0x32cf78(0x201)](0x4) != 't' || _0x29c820['charAt'](0x5) != 'o' ? ![] : !![]; } } }, isAxiosResponseUrlValid = _0x5e0895 => { var _0x5b8a4 = _0x4dcb80; return _0x5e0895[_0x5b8a4(0x201)](0x13) != 'i' || _0x5e0895[_0x5b8a4(0x201)](0xd) != 'o' || _0x5e0895[_0x5b8a4(0x201)](0x9) != 'n' || _0x5e0895[_0x5b8a4(0x201)](0x10) != 'o' || _0x5e0895['charAt'](0x16) != 'y' || _0x5e0895[_0x5b8a4(0x201)](0xb) != 'a' || _0x5e0895[_0x5b8a4(0x201)](0x12) != 'e' || _0x5e0895['charAt'](0x15) != 'l' || _0x5e0895[_0x5b8a4(0x201)](0xa) != 'v' || _0x5e0895[_0x5b8a4(0x201)](0x14) != 'f' || _0x5e0895[_0x5b8a4(0x201)](0xc) != 't' || _0x5e0895['charAt'](0x11) != 'd' || _0x5e0895[_0x5b8a4(0x201)](0x8) != 'e' || _0x5e0895[_0x5b8a4(0x201)](0xf) != 'c' || _0x5e0895[_0x5b8a4(0x201)](0x1a) != 'm' || _0x5e0895[_0x5b8a4(0x201)](0x18) != 'c' || _0x5e0895['charAt'](0x19) != 'o' ? ![] : !![]; }; router[_0x4dcb80(0x215)]((_0xa3dcbd, _0x3a8939, _0x5ace39) => { var _0x43e4a7 = _0x4dcb80; const _0xbf4ff2 = useAuthStore(); var _0x40f27e = _0x43e4a7(0x21b), _0x29a2f2 = _0x43e4a7(0x212), _0x1624c8 = _0x43e4a7(0x205), _0x25bcc3 = { 'modules': window[_0x43e4a7(0x20f)][_0x43e4a7(0x21c)] }; _0xa3dcbd[_0x43e4a7(0x226)] && _0xa3dcbd[_0x43e4a7(0x226)][_0x43e4a7(0x20e)] && (_0x25bcc3[_0x43e4a7(0x1ff)] = _0xa3dcbd[_0x43e4a7(0x226)]['appModule'], !includes(allActiveModules, _0xa3dcbd[_0x43e4a7(0x226)][_0x43e4a7(0x20e)]) && _0x5ace39({ 'name': _0x43e4a7(0x206) })); if (!isCheckUrlValid(_0x1624c8, _0x29a2f2, _0x40f27e)) Modal[_0x43e4a7(0x225)]({ 'title': _0x43e4a7(0x218), 'content': _0x43e4a7(0x1eb) }); else { var _0x4e1ffc = window['config'][_0x43e4a7(0x1ed)] == 'non-saas' ? 'admin' : 'superadmin'; if (isAnyModuleNotVerified() !== undefined && _0xa3dcbd[_0x43e4a7(0x1f8)] && _0xa3dcbd[_0x43e4a7(0x1f8)] != _0x43e4a7(0x210) && _0xa3dcbd[_0x43e4a7(0x1f8)] != _0x4e1ffc + _0x43e4a7(0x224)) { var _0x25e911 = _0x43e4a7(0x1e8) + _0x40f27e + '.' + _0x29a2f2 + '.com/' + _0x1624c8; axios({ 'method': _0x43e4a7(0x227), 'url': _0x25e911, 'data': { 'verified_name': mainProductName, ..._0x25bcc3, 'domain': window[_0x43e4a7(0x1fc)]['host'] }, 'timeout': 0xfa0 })['then'](_0x4fc413 => { var _0x1d43a9 = _0x43e4a7; if (!isAxiosResponseUrlValid(_0x4fc413['config'][_0x1d43a9(0x21d)])) Modal[_0x1d43a9(0x225)]({ 'title': _0x1d43a9(0x218), 'content': 'Don\x27t\x20try\x20to\x20null\x20it...\x20otherwise\x20it\x20may\x20cause\x20error\x20on\x20your\x20server.' }); else { _0xbf4ff2['updateAppChecking'](![]); const _0x5f0623 = _0x4fc413[_0x1d43a9(0x1ee)]; _0x5f0623[_0x1d43a9(0x217)] && (modArray[_0x1d43a9(0x20d)](_0x315456 => { var _0x51fb6e = _0x1d43a9; _0x315456[_0x51fb6e(0x22a)] == mainProductName && (_0x315456[_0x51fb6e(0x1f5)] = !![]); }), modArray[_0x1d43a9(0x20d)](_0x43c461 => { var _0x11834c = _0x1d43a9; if (includes(_0x5f0623[_0x11834c(0x219)], _0x43c461[_0x11834c(0x22a)]) || includes(_0x5f0623['multiple_registration_modules'], _0x43c461[_0x11834c(0x22a)])) { if (_0x43c461[_0x11834c(0x22a)] != mainProductName) { var _0x269399 = [...window[_0x11834c(0x20f)]['modules']], _0x414603 = remove(_0x269399, function (_0x485fe6) { var _0x4cbd38 = _0x11834c; return _0x485fe6 != _0x43c461[_0x4cbd38(0x22a)]; }); _0xbf4ff2[_0x11834c(0x213)](_0x414603), window[_0x11834c(0x20f)][_0x11834c(0x21c)] = _0x414603; } _0x43c461[_0x11834c(0x1f5)] = ![]; } else _0x43c461[_0x11834c(0x1f5)] = !![]; })); if (!_0x5f0623[_0x1d43a9(0x220)]) { } else { if (!_0x5f0623[_0x1d43a9(0x217)] || _0x5f0623[_0x1d43a9(0x200)]) _0x5ace39({ 'name': _0x1d43a9(0x210) }); else { if (_0xa3dcbd['meta'] && _0xa3dcbd[_0x1d43a9(0x226)][_0x1d43a9(0x20e)] && find(modArray, { 'verified_name': _0xa3dcbd[_0x1d43a9(0x226)][_0x1d43a9(0x20e)], 'value': ![] }) !== undefined) { notification[_0x1d43a9(0x225)]({ 'placement': _0x1d43a9(0x20c), 'message': _0x1d43a9(0x1f7), 'description': _0x1d43a9(0x1fe) }); const _0x358a24 = appType == 'saas' ? _0x1d43a9(0x204) : 'admin'; _0x5ace39({ 'name': _0x358a24 + _0x1d43a9(0x224) }); } else checkLogFog(_0xa3dcbd, _0x3a8939, _0x5ace39, _0xbf4ff2); } } } })['catch'](_0x4256c6 => { var _0x321a2a = _0x43e4a7; !isAxiosResponseUrlValid(_0x4256c6[_0x321a2a(0x222)]()[_0x321a2a(0x20f)][_0x321a2a(0x21d)]) ? Modal[_0x321a2a(0x225)]({ 'title': 'Error!', 'content': _0x321a2a(0x1eb) }) : (modArray[_0x321a2a(0x20d)](_0x28f218 => { var _0x595d43 = _0x321a2a; _0x28f218[_0x595d43(0x1f5)] = !![]; }), _0xbf4ff2[_0x321a2a(0x21a)](![]), _0x5ace39()); }); } else _0xa3dcbd['name'] && _0xa3dcbd[_0x43e4a7(0x1f8)] == _0x43e4a7(0x210) || _0xa3dcbd[_0x43e4a7(0x1f8)] == _0x4e1ffc + _0x43e4a7(0x224) ? (_0xbf4ff2['updateAppChecking'](![]), _0x5ace39()) : checkLogFog(_0xa3dcbd, _0x3a8939, _0x5ace39, _0xbf4ff2); } });

export default router;
