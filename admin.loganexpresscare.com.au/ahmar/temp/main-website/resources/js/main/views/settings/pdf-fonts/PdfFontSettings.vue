<template>
    <div>
        <template v-if="customType == 'menu'">
            <a-menu-item @click="showAdd" key="pdf_fonts">
                <template #icon>
                    <slot name="icon"></slot>
                </template>
                <slot></slot>
            </a-menu-item>
        </template>
        <template v-else>
            <a-tooltip
                v-if="tooltip"
                placement="topLeft"
                :title="$t('pdf_font.add')"
                arrow-point-at-center
            >
                <a-button
                    @click="showAdd"
                    class="ml-5 no-border-radius"
                    :type="btnType"
                >
                    <template #icon> <slot name="icon"></slot> </template>
                    <slot></slot>
                </a-button>
            </a-tooltip>
            <a-button
                v-else
                @click="showAdd"
                class="ml-5 no-border-radius"
                :type="btnType"
            >
                <template #icon><slot name="icon"></slot> </template>
                <slot></slot>
            </a-button>
        </template>
        <a-drawer
            :title="$t('pdf_font.font_settings')"
            :width="drawerWidth"
            :visible="visible"
            :body-style="{ paddingBottom: '80px' }"
            :footer-style="{ textAlign: 'right' }"
            :maskClosable="false"
            @close="onClose"
        >
            <a-form layout="vertical">
                <a-tabs v-model:activeKey="activeKey" :tabBarGutter="46">
                    <a-tab-pane key="font_settings">
                        <template #tab>
                            <span>
                                <SettingOutlined />
                                {{ $t("pdf_font.font_settings") }}
                            </span>
                        </template>
                        <a-row :gutter="16">
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="$t('pdf_font.use_custom_font')"
                                    name="use_custom_font"
                                    :help="
                                        rules.use_custom_font
                                            ? rules.use_custom_font.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.use_custom_font ? 'error' : null
                                    "
                                >
                                    <a-radio-group
                                        v-model:value="
                                            newFormData.use_custom_font
                                        "
                                        button-style="solid"
                                        size="small"
                                    >
                                        <a-radio-button :value="1">
                                            {{ $t("common.yes") }}
                                        </a-radio-button>
                                        <a-radio-button :value="0">
                                            {{ $t("common.no") }}
                                        </a-radio-button>
                                    </a-radio-group>
                                </a-form-item>
                            </a-col>
                        </a-row>
                        <a-row :gutter="16">
                            <a-col
                                :xs="24"
                                :sm="24"
                                :md="24"
                                :lg="24"
                                v-if="newFormData.use_custom_font == 1"
                            >
                                <a-form-item
                                    :label="$t('menu.pdf_fonts')"
                                    name="pdf_font_id"
                                    :help="
                                        rules.pdf_font_id
                                            ? rules.pdf_font_id.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.pdf_font_id ? 'error' : null
                                    "
                                    class="required"
                                >
                                    <span style="display: flex">
                                        <a-select
                                            v-model:value="
                                                newFormData.pdf_font_id
                                            "
                                            :placeholder="
                                                $t(
                                                    'common.select_default_text',
                                                    [$t('menu.pdf_fonts')]
                                                )
                                            "
                                            :allowClear="true"
                                            optionFilterProp="label"
                                            show-search
                                        >
                                            <a-select-option
                                                v-for="pdfFont in pdfFonts"
                                                :key="pdfFont.xid"
                                                :value="pdfFont.xid"
                                                :label="pdfFont.name"
                                            >
                                                {{ pdfFont.name }}
                                            </a-select-option>
                                        </a-select>
                                        <PdfFontAddButton
                                            @onAddSuccess="pdfFontAdded"
                                        />
                                    </span>
                                </a-form-item>
                            </a-col>
                        </a-row>
                    </a-tab-pane>
                    <a-tab-pane key="appearance">
                        <template #tab>
                            <span>
                                <FileTextOutlined />
                                {{ $t("pdf_font.appearance") }}
                            </span>
                        </template>
                        <a-row :gutter="16">
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t(
                                            'hrm_settings.letterhead_header_color'
                                        )
                                    "
                                    name="letterhead_header_color"
                                    :help="
                                        rules.letterhead_header_color
                                            ? rules.letterhead_header_color
                                                  .message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.letterhead_header_color
                                            ? 'error'
                                            : null
                                    "
                                >
                                    <color-picker
                                        v-model:pureColor="
                                            newFormData.letterhead_header_color
                                        "
                                        format="hex"
                                        useType="pure"
                                        v-model:gradientColor="gradientColor"
                                    />
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t(
                                            'hrm_settings.letterhead_show_company_name'
                                        )
                                    "
                                    name="letterhead_show_company_name"
                                    :help="
                                        rules.letterhead_show_company_name
                                            ? rules.letterhead_show_company_name
                                                  .message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.letterhead_show_company_name
                                            ? 'error'
                                            : null
                                    "
                                >
                                    <a-radio-group
                                        v-model:value="
                                            newFormData.letterhead_show_company_name
                                        "
                                        button-style="solid"
                                        size="small"
                                    >
                                        <a-radio-button :value="1">
                                            {{ $t("common.yes") }}
                                        </a-radio-button>
                                        <a-radio-button :value="0">
                                            {{ $t("common.no") }}
                                        </a-radio-button>
                                    </a-radio-group>
                                </a-form-item>
                            </a-col>
                        </a-row>
                        <a-row :gutter="16">
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t(
                                            'hrm_settings.letterhead_show_company_email'
                                        )
                                    "
                                    name="letterhead_show_company_email"
                                    :help="
                                        rules.letterhead_show_company_email
                                            ? rules
                                                  .letterhead_show_company_email
                                                  .message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.letterhead_show_company_email
                                            ? 'error'
                                            : null
                                    "
                                >
                                    <a-radio-group
                                        v-model:value="
                                            newFormData.letterhead_show_company_email
                                        "
                                        button-style="solid"
                                        size="small"
                                    >
                                        <a-radio-button :value="1">
                                            {{ $t("common.yes") }}
                                        </a-radio-button>
                                        <a-radio-button :value="0">
                                            {{ $t("common.no") }}
                                        </a-radio-button>
                                    </a-radio-group>
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t(
                                            'hrm_settings.letterhead_show_company_phone'
                                        )
                                    "
                                    name="letterhead_show_company_phone"
                                    :help="
                                        rules.letterhead_show_company_phone
                                            ? rules
                                                  .letterhead_show_company_phone
                                                  .message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.letterhead_show_company_phone
                                            ? 'error'
                                            : null
                                    "
                                >
                                    <a-radio-group
                                        v-model:value="
                                            newFormData.letterhead_show_company_phone
                                        "
                                        button-style="solid"
                                        size="small"
                                    >
                                        <a-radio-button :value="1">
                                            {{ $t("common.yes") }}
                                        </a-radio-button>
                                        <a-radio-button :value="0">
                                            {{ $t("common.no") }}
                                        </a-radio-button>
                                    </a-radio-group>
                                </a-form-item>
                            </a-col>

                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t(
                                            'hrm_settings.letterhead_show_company_address'
                                        )
                                    "
                                    name="letterhead_show_company_address"
                                    :help="
                                        rules.letterhead_show_company_address
                                            ? rules
                                                  .letterhead_show_company_address
                                                  .message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.letterhead_show_company_address
                                            ? 'error'
                                            : null
                                    "
                                >
                                    <a-radio-group
                                        v-model:value="
                                            newFormData.letterhead_show_company_address
                                        "
                                        button-style="solid"
                                        size="small"
                                    >
                                        <a-radio-button :value="1">
                                            {{ $t("common.yes") }}
                                        </a-radio-button>
                                        <a-radio-button :value="0">
                                            {{ $t("common.no") }}
                                        </a-radio-button>
                                    </a-radio-group>
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t(
                                            'hrm_settings.letterhead_title_show_in_pdf'
                                        )
                                    "
                                    name="letterhead_title_show_in_pdf"
                                    :help="
                                        rules.letterhead_title_show_in_pdf
                                            ? rules.letterhead_title_show_in_pdf
                                                  .message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.letterhead_title_show_in_pdf
                                            ? 'error'
                                            : null
                                    "
                                >
                                    <a-radio-group
                                        v-model:value="
                                            newFormData.letterhead_title_show_in_pdf
                                        "
                                        button-style="solid"
                                        size="small"
                                    >
                                        <a-radio-button :value="1">
                                            {{ $t("common.yes") }}
                                        </a-radio-button>
                                        <a-radio-button :value="0">
                                            {{ $t("common.no") }}
                                        </a-radio-button>
                                    </a-radio-group>
                                </a-form-item>
                            </a-col>
                        </a-row>
                    </a-tab-pane>
                    <a-tab-pane key="spacing">
                        <template #tab>
                            <span>
                                <ControlOutlined />
                                {{ $t("pdf_font.spacing") }}
                            </span>
                        </template>

                        <FormItemHeading>
                            {{ $t("hrm_settings.letterhead_spacing") }}
                        </FormItemHeading>
                        <a-row :gutter="16">
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t('hrm_settings.letterhead_left_space')
                                    "
                                    name="letterhead_left_space"
                                    :help="
                                        rules.letterhead_left_space
                                            ? rules.letterhead_left_space
                                                  .message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.letterhead_left_space
                                            ? 'error'
                                            : null
                                    "
                                >
                                    <a-input-number
                                        v-model:value="
                                            newFormData.letterhead_left_space
                                        "
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [
                                                    $t(
                                                        'hrm_settings.letterhead_left_space'
                                                    ),
                                                ]
                                            )
                                        "
                                        style="width: 100%"
                                    >
                                        <template #addonAfter>
                                            {{ $t("common.mm") }}
                                        </template>
                                    </a-input-number>
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t(
                                            'hrm_settings.letterhead_right_space'
                                        )
                                    "
                                    name="letterhead_right_space"
                                    :help="
                                        rules.letterhead_right_space
                                            ? rules.letterhead_right_space
                                                  .message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.letterhead_right_space
                                            ? 'error'
                                            : null
                                    "
                                >
                                    <a-input-number
                                        v-model:value="
                                            newFormData.letterhead_right_space
                                        "
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [
                                                    $t(
                                                        'hrm_settings.letterhead_right_space'
                                                    ),
                                                ]
                                            )
                                        "
                                        style="width: 100%"
                                    >
                                        <template #addonAfter>
                                            {{ $t("common.mm") }}
                                        </template>
                                    </a-input-number>
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t('hrm_settings.letterhead_top_space')
                                    "
                                    name="letterhead_top_space"
                                    :help="
                                        rules.letterhead_top_space
                                            ? rules.letterhead_top_space.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.letterhead_top_space
                                            ? 'error'
                                            : null
                                    "
                                >
                                    <a-input-number
                                        v-model:value="
                                            newFormData.letterhead_top_space
                                        "
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [
                                                    $t(
                                                        'hrm_settings.letterhead_top_space'
                                                    ),
                                                ]
                                            )
                                        "
                                        style="width: 100%"
                                    >
                                        <template #addonAfter>
                                            {{ $t("common.mm") }}
                                        </template>
                                    </a-input-number>
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t(
                                            'hrm_settings.letterhead_bottom_space'
                                        )
                                    "
                                    name="letterhead_bottom_space"
                                    :help="
                                        rules.letterhead_bottom_space
                                            ? rules.letterhead_bottom_space
                                                  .message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.letterhead_bottom_space
                                            ? 'error'
                                            : null
                                    "
                                >
                                    <a-input-number
                                        v-model:value="
                                            newFormData.letterhead_bottom_space
                                        "
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [
                                                    $t(
                                                        'hrm_settings.letterhead_bottom_space'
                                                    ),
                                                ]
                                            )
                                        "
                                        style="width: 100%"
                                    >
                                        <template #addonAfter>
                                            {{ $t("common.mm") }}
                                        </template>
                                    </a-input-number>
                                </a-form-item>
                            </a-col>
                        </a-row>
                        <FormItemHeading>
                            {{ $t("hrm_settings.export_spacing") }}
                        </FormItemHeading>
                        <a-row :gutter="16">
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t('hrm_settings.export_pdf_left_space')
                                    "
                                    name="export_pdf_left_space"
                                    :help="
                                        rules.export_pdf_left_space
                                            ? rules.export_pdf_left_space
                                                  .message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.export_pdf_left_space
                                            ? 'error'
                                            : null
                                    "
                                >
                                    <a-input-number
                                        v-model:value="
                                            newFormData.export_pdf_left_space
                                        "
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [
                                                    $t(
                                                        'hrm_settings.export_pdf_left_space'
                                                    ),
                                                ]
                                            )
                                        "
                                        style="width: 100%"
                                    >
                                        <template #addonAfter>
                                            {{ $t("common.mm") }}
                                        </template>
                                    </a-input-number>
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t(
                                            'hrm_settings.export_pdf_right_space'
                                        )
                                    "
                                    name="export_pdf_right_space"
                                    :help="
                                        rules.export_pdf_right_space
                                            ? rules.export_pdf_right_space
                                                  .message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.export_pdf_right_space
                                            ? 'error'
                                            : null
                                    "
                                >
                                    <a-input-number
                                        v-model:value="
                                            newFormData.export_pdf_right_space
                                        "
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [
                                                    $t(
                                                        'hrm_settings.export_pdf_right_space'
                                                    ),
                                                ]
                                            )
                                        "
                                        style="width: 100%"
                                    >
                                        <template #addonAfter>
                                            {{ $t("common.mm") }}
                                        </template>
                                    </a-input-number>
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t('hrm_settings.export_pdf_top_space')
                                    "
                                    name="export_pdf_top_space"
                                    :help="
                                        rules.export_pdf_top_space
                                            ? rules.export_pdf_top_space.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.export_pdf_top_space
                                            ? 'error'
                                            : null
                                    "
                                >
                                    <a-input-number
                                        v-model:value="
                                            newFormData.export_pdf_top_space
                                        "
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [
                                                    $t(
                                                        'hrm_settings.export_pdf_top_space'
                                                    ),
                                                ]
                                            )
                                        "
                                        style="width: 100%"
                                    >
                                        <template #addonAfter>
                                            {{ $t("common.mm") }}
                                        </template>
                                    </a-input-number>
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t(
                                            'hrm_settings.export_pdf_bottom_space'
                                        )
                                    "
                                    name="export_pdf_bottom_space"
                                    :help="
                                        rules.export_pdf_bottom_space
                                            ? rules.export_pdf_bottom_space
                                                  .message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.export_pdf_bottom_space
                                            ? 'error'
                                            : null
                                    "
                                >
                                    <a-input-number
                                        v-model:value="
                                            newFormData.export_pdf_bottom_space
                                        "
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [
                                                    $t(
                                                        'hrm_settings.export_pdf_bottom_space'
                                                    ),
                                                ]
                                            )
                                        "
                                        style="width: 100%"
                                    >
                                        <template #addonAfter>
                                            {{ $t("common.mm") }}
                                        </template>
                                    </a-input-number>
                                </a-form-item>
                            </a-col>
                        </a-row>
                    </a-tab-pane>
                    <a-tab-pane key="pdf_font_size">
                        <template #tab>
                            <span>
                                <FormOutlined />
                                {{ $t("pdf_font.pdf_font_size") }}
                            </span>
                        </template>

                        <FormItemHeading>
                            {{ $t("pdf_font.holiday_pdf") }}
                        </FormItemHeading>

                        <a-row :gutter="16">
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t('hrm_settings.holiday_pdf_font_size')
                                    "
                                    name="holiday_pdf_font_size"
                                    :help="
                                        rules.holiday_pdf_font_size
                                            ? rules.holiday_pdf_font_size
                                                  .message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.holiday_pdf_font_size
                                            ? 'error'
                                            : null
                                    "
                                >
                                    <a-input-number
                                        v-model:value="
                                            newFormData.holiday_pdf_font_size
                                        "
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [
                                                    $t(
                                                        'hrm_settings.holiday_pdf_font_size'
                                                    ),
                                                ]
                                            )
                                        "
                                        style="width: 100%"
                                    >
                                        <template #addonAfter>
                                            {{ $t("common.px") }}
                                        </template>
                                    </a-input-number>
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t(
                                            'hrm_settings.holiday_pdf_line_height'
                                        )
                                    "
                                    name="holiday_pdf_line_height"
                                    :help="
                                        rules.holiday_pdf_line_height
                                            ? rules.holiday_pdf_line_height
                                                  .message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.holiday_pdf_line_height
                                            ? 'error'
                                            : null
                                    "
                                >
                                    <a-input-number
                                        v-model:value="
                                            newFormData.holiday_pdf_line_height
                                        "
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [
                                                    $t(
                                                        'hrm_settings.holiday_pdf_line_height'
                                                    ),
                                                ]
                                            )
                                        "
                                        style="width: 100%"
                                    >
                                        <template #addonAfter>
                                            {{ $t("common.px") }}
                                        </template>
                                    </a-input-number>
                                </a-form-item>
                            </a-col>
                        </a-row>

                        <form-item-heading>
                            {{ $t("pdf_font.generate_pdf") }}
                        </form-item-heading>

                        <a-row :gutter="16">
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t(
                                            'hrm_settings.generate_pdf_font_size'
                                        )
                                    "
                                    name="generate_pdf_font_size"
                                    :help="
                                        rules.generate_pdf_font_size
                                            ? rules.generate_pdf_font_size
                                                  .message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.generate_pdf_font_size
                                            ? 'error'
                                            : null
                                    "
                                >
                                    <a-input-number
                                        v-model:value="
                                            newFormData.generate_pdf_font_size
                                        "
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [
                                                    $t(
                                                        'hrm_settings.generate_pdf_font_size'
                                                    ),
                                                ]
                                            )
                                        "
                                        style="width: 100%"
                                    >
                                        <template #addonAfter>
                                            {{ $t("common.px") }}
                                        </template>
                                    </a-input-number>
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t(
                                            'hrm_settings.generate_pdf_line_height'
                                        )
                                    "
                                    name="generate_pdf_line_height"
                                    :help="
                                        rules.generate_pdf_line_height
                                            ? rules.generate_pdf_line_height
                                                  .message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.generate_pdf_line_height
                                            ? 'error'
                                            : null
                                    "
                                >
                                    <a-input-number
                                        v-model:value="
                                            newFormData.generate_pdf_line_height
                                        "
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [
                                                    $t(
                                                        'hrm_settings.generate_pdf_line_height'
                                                    ),
                                                ]
                                            )
                                        "
                                        style="width: 100%"
                                    >
                                        <template #addonAfter>
                                            {{ $t("common.px") }}
                                        </template>
                                    </a-input-number>
                                </a-form-item>
                            </a-col>
                        </a-row>
                        <form-item-heading>
                            {{ $t("hrm_settings.export_pdf") }}
                        </form-item-heading>

                        <a-row :gutter="16">
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t('hrm_settings.export_pdf_font_size')
                                    "
                                    name="export_pdf_font_size"
                                    :help="
                                        rules.export_pdf_font_size
                                            ? rules.export_pdf_font_size.message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.export_pdf_font_size
                                            ? 'error'
                                            : null
                                    "
                                >
                                    <a-input-number
                                        v-model:value="
                                            newFormData.export_pdf_font_size
                                        "
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [
                                                    $t(
                                                        'hrm_settings.export_pdf_font_size'
                                                    ),
                                                ]
                                            )
                                        "
                                        style="width: 100%"
                                    >
                                        <template #addonAfter>
                                            {{ $t("common.px") }}
                                        </template>
                                    </a-input-number>
                                </a-form-item>
                            </a-col>
                            <a-col :xs="24" :sm="24" :md="12" :lg="12">
                                <a-form-item
                                    :label="
                                        $t(
                                            'hrm_settings.export_pdf_line_height'
                                        )
                                    "
                                    name="export_pdf_line_height"
                                    :help="
                                        rules.export_pdf_line_height
                                            ? rules.export_pdf_line_height
                                                  .message
                                            : null
                                    "
                                    :validateStatus="
                                        rules.export_pdf_line_height
                                            ? 'error'
                                            : null
                                    "
                                >
                                    <a-input-number
                                        v-model:value="
                                            newFormData.export_pdf_line_height
                                        "
                                        :placeholder="
                                            $t(
                                                'common.placeholder_default_text',
                                                [
                                                    $t(
                                                        'hrm_settings.export_pdf_line_height'
                                                    ),
                                                ]
                                            )
                                        "
                                        style="width: 100%"
                                    >
                                        <template #addonAfter>
                                            {{ $t("common.px") }}
                                        </template>
                                    </a-input-number>
                                </a-form-item>
                            </a-col>
                        </a-row>
                    </a-tab-pane>
                </a-tabs>
            </a-form>
            <template #footer>
                <a-button
                    type="primary"
                    @click="onSubmit"
                    style="margin-right: 8px"
                    :loading="loading"
                >
                    <template #icon> <SaveOutlined /> </template>
                    {{ $t("common.update") }}
                </a-button>
                <a-button @click="onClose">
                    {{ $t("common.cancel") }}
                </a-button>
            </template>
        </a-drawer>
    </div>
</template>

<script>
import { defineComponent, onMounted, ref, watch } from "vue";
import {
    PlusOutlined,
    LoadingOutlined,
    SaveOutlined,
    SettingOutlined,
    FileTextOutlined,
    ControlOutlined,
    FormOutlined,
} from "@ant-design/icons-vue";
import { useI18n } from "vue-i18n";
import apiAdmin from "../../../../common/composable/apiAdmin";
import common from "../../../../common/composable/common";
import { ColorPicker } from "vue3-colorpicker";
import "vue3-colorpicker/style.css";
import PdfFontAddButton from "./AddButton.vue";
import { useAuthStore } from "../../../store/authStore";
import FormItemHeading from "../../../../common/components/common/typography/FormItemHeading.vue";

export default defineComponent({
    components: {
        PlusOutlined,
        LoadingOutlined,
        SaveOutlined,
        SettingOutlined,
        FileTextOutlined,
        ControlOutlined,
        FormOutlined,
        ColorPicker,
        PdfFontAddButton,
        FormItemHeading,
    },
    props: {
        btnType: {
            default: "default",
        },
        tooltip: {
            default: false,
        },
        customType: {
            default: "button",
        },
    },
    setup(props, { emit }) {
        const { addEditRequestAdmin, loading, rules } = apiAdmin();
        const { permsArray, appSetting } = common();
        const authStore = useAuthStore();
        const { t } = useI18n();
        const pdfFonts = ref([]);
        const pdfFontUrl = "pdf-fonts";
        const activeKey = ref("font_settings");
        const gradientColor = ref(
            "linear-gradient(0deg, rgba(0, 0, 0, 1) 0%, rgba(0, 0, 0, 1) 100%)"
        );
        const visible = ref(false);

        const showAdd = () => {
            activeKey.value = "font_settings";
            visible.value = true;
        };

        const onClose = () => {
            rules.value = {};
            visible.value = false;
        };
        const newFormData = ref({
            use_custom_font: 0,
            pdf_font_id: undefined,
            letterhead_header_color: appSetting.value.letterhead_header_color,
            letterhead_show_company_name:
                appSetting.value.letterhead_show_company_name,
            letterhead_show_company_email:
                appSetting.value.letterhead_show_company_email,
            letterhead_show_company_phone:
                appSetting.value.letterhead_show_company_phone,
            letterhead_show_company_address:
                appSetting.value.letterhead_show_company_address,
            letterhead_left_space: appSetting.value.letterhead_left_space,
            letterhead_right_space: appSetting.value.letterhead_right_space,
            letterhead_top_space: appSetting.value.letterhead_top_space,
            letterhead_bottom_space: appSetting.value.letterhead_bottom_space,
            holiday_pdf_font_size: appSetting.value.holiday_pdf_font_size,
            holiday_pdf_line_height: appSetting.value.holiday_pdf_line_height,
            generate_pdf_font_size: appSetting.value.generate_pdf_font_size,
            generate_pdf_line_height: appSetting.value.generate_pdf_line_height,
            letterhead_title_show_in_pdf:
                appSetting.value.letterhead_title_show_in_pdf,
            export_pdf_font_size: appSetting.value.export_pdf_font_size,
            export_pdf_line_height: appSetting.value.export_pdf_line_height,
            export_pdf_left_space: appSetting.value.export_pdf_left_space,
            export_pdf_right_space: appSetting.value.export_pdf_right_space,
            export_pdf_top_space: appSetting.value.export_pdf_top_space,
            export_pdf_bottom_space: appSetting.value.export_pdf_bottom_space,
        });

        const pdfFintData = () => {
            const pdfFontPromise = axiosAdmin.get(pdfFontUrl);

            Promise.all([pdfFontPromise]).then(([pdfFontsResponse]) => {
                pdfFonts.value = pdfFontsResponse.data;
            });
        };

        const onSubmit = () => {
            addEditRequestAdmin({
                url: "pdf-custom-fonts",
                data: newFormData.value,
                successMessage: t("pdf_font.updated"),
                success: (res) => {
                    authStore.updateAppAction();
                    visible.value = false;
                },
            });
        };

        const pdfFontAdded = () => {
            axiosAdmin.get(pdfFontUrl).then((response) => {
                pdfFonts.value = response.data;
            });
        };

        watch(
            () => visible,
            (newVal, oldVal) => {
                if (newVal) {
                    pdfFintData();
                    newFormData.value.use_custom_font =
                        appSetting.value.use_custom_font != 0
                            ? appSetting.value.use_custom_font
                            : 0;
                    newFormData.value.pdf_font_id =
                        appSetting.value.x_pdf_font_id != null
                            ? appSetting.value.x_pdf_font_id
                            : undefined;
                }
            }
        );

        return {
            loading,
            permsArray,
            rules,
            onSubmit,
            onClose,
            pdfFonts,
            newFormData,
            activeKey,
            gradientColor,
            pdfFontAdded,
            visible,
            showAdd,
            onClose,
            drawerWidth: window.innerWidth <= 991 ? "90%" : "40%",
        };
    },
});
</script>
