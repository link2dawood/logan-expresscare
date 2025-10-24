import { useI18n } from "vue-i18n";
import { ref } from "vue";

const fields = () => {
    const url =
        "self/generates?fields=id,xid,user_id,x_user_id,user{id,xid,name},description,left_space,right_space,top_space,bottom_space,letterhead_template_id,x_letterhead_template_id,letterHeadTemplate{id,xid,title,description}";
    const addEditUrl = "generates";
    const hashableColumns = ["user_id", "letterhead_template_id"];
    const { t } = useI18n();
    const descriptionContent = ref("");
    const cssStyles = ref({
        height: "80vh",
        overflow: "auto",
        paddingTop: "0px",
        paddingRight: "0px",
        paddingBottom: "0px",
        paddingLeft: "0px",
    });

    const formData = ref({
        user_id: undefined,
        letterhead_template_id: undefined,
        left_space: 20,
        right_space: 20,
        top_space: 20,
        bottom_space: 20,
        description: "",
    });

    const columns = [
        {
            title: t("generate.letter_head_template"),
            dataIndex: "letterhead_template_id",
        },

        {
            title: t("generate.created_date"),
            dataIndex: "created_at",
        },
        {
            title: t("common.action"),
            dataIndex: "action",
        },
    ];

    const filterableColumns = [
        {
            key: "letterhead_template_id",
            value: t("generate.letter_head_template"),
        },
    ];

    const changeStyles = () => {
        cssStyles.value = {
            height: "80vh",
            overflow: "auto",
            paddingTop: `${formData.value.top_space}px`,
            paddingRight: `${formData.value.right_space}px`,
            paddingBottom: `${formData.value.bottom_space}px`,
            paddingLeft: `${formData.value.left_space}px`,
        };
    };

    return {
        url,
        addEditUrl,
        formData,
        columns,
        filterableColumns,
        hashableColumns,
        cssStyles,
        changeStyles,
        descriptionContent,
    };
};

export default fields;
