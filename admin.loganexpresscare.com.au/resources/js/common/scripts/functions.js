import { forEach } from "lodash-es";

const checkUserPermission = (permissionName, user) => {
    var permissionAllowed = user.role.name == "admin" ? true : false;

    forEach(user.role.perms, (permission) => {
        if (permission.name == permissionName) {
            permissionAllowed = true;
        }
    });

    return permissionAllowed;
};


const getUrlByAppType = (pathUrl) => {
    const appType = window.config.app_type;
    return appType == 'non-saas' ? pathUrl : `superadmin/${pathUrl}`;
}

export {
    checkUserPermission,
    getUrlByAppType,
};
