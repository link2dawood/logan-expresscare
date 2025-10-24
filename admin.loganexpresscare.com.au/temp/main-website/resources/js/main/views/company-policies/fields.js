import { useI18n } from "vue-i18n";

const fields = () => {
    const url =
        "company-policies?fields=id,xid,location_id,x_location_id,title,method_type,letter_description,policy_document,policy_document_url,location{id,xid,name},description";
    const addEditUrl = "company-policies";
    const hashableColumns = ["location_id"];
    const { t } = useI18n();

    const initData = {
        location_id: undefined,
        title: "",
        policy_document: undefined,
        policy_document_url: undefined,
        description: "",
        method_type: "upload",
        letter_description: "",
    };

    const columns = [
        {
            title: t("company_policy.location"),
            dataIndex: "location_id",
        },
        {
            title: t("company_policy.title"),
            dataIndex: "title",
        },
        {
            title: t("company_policy.description"),
            dataIndex: "description",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "title",
            value: t("common.title"),
        },
    ];

    return {
        url,
        addEditUrl,
        initData,
        columns,
        filterableColumns,
        hashableColumns,
    };
};

export default fields;
