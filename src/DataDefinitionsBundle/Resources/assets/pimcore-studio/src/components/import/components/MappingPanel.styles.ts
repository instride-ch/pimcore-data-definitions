/**
 * MappingPanel Styles - Grid-based layout with groupings
 */

import { createStyles } from 'antd-style'

export const useStyles = createStyles(({ token, css }) => ({
  container: css`
    height: 100%;
    overflow: auto;
    background: ${token.colorBgContainer};
  `,

  loadingContainer: css`
    display: flex;
    justify-content: center;
    align-items: center;
    height: 200px;
  `,

  mappingTable: css`
    .ant-table-thead > tr > th {
      background: ${token.colorFillAlter};
      font-weight: 600;
      padding: 8px 12px;
      border-bottom: 1px solid ${token.colorBorderSecondary};
    }

    .ant-table-tbody > tr > td {
      padding: 4px 12px;
      vertical-align: middle;
    }

    .ant-table-tbody > tr:hover > td {
      background: ${token.colorFillTertiary};
    }
  `,

  groupHeaderRow: css`
    background: ${token.colorFillSecondary} !important;

    td {
      background: ${token.colorFillSecondary} !important;
      font-weight: 500;
    }

    &:hover td {
      background: ${token.colorFillTertiary} !important;
    }
  `,

  mappingRow: css`
    &:hover {
      background: ${token.colorFillTertiary};
    }
  `,

  groupRow: css`
    display: flex;
    align-items: center;
    user-select: none;
    padding: 4px 0;
  `,

  groupName: css`
    font-weight: 500;
    margin-right: 8px;
  `,

  groupCount: css`
    color: ${token.colorTextSecondary};
    font-size: 12px;
  `,

  fieldRow: css`
    display: flex;
    align-items: center;
  `,

  actionButtons: css`
    display: flex;
    gap: 4px;
    justify-content: flex-end;
  `,

  // Keep old styles for backwards compatibility
  panel: css`
    height: 100%;
    display: flex;
    flex-direction: column;
    background: ${token.colorBgContainer};
  `,

  panelHeader: css`
    padding: 8px 12px;
    border-bottom: 1px solid ${token.colorBorderSecondary};
    background: ${token.colorFillAlter};
  `,

  panelTitle: css`
    margin: 0 !important;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  `,

  panelBody: css`
    flex: 1;
    overflow: auto;
    padding: 8px;
  `,

  loading: css`
    padding: 20px;
    text-align: center;
    color: ${token.colorTextSecondary};
  `,

  groupTitle: css`
    font-weight: 500;
    color: ${token.colorText};
  `,

  fieldItem: css`
    display: flex;
    align-items: center;
    gap: 8px;
  `,

  fieldName: css`
    color: ${token.colorText};
  `,

  fieldType: css`
    color: ${token.colorTextSecondary};
    font-size: 11px;
    text-transform: uppercase;
  `,

  mappingItem: css`
    display: flex;
    align-items: center;
    gap: 8px;
  `,

  mappingName: css`
    color: ${token.colorText};
    font-weight: 500;
  `,

  mappingFrom: css`
    color: ${token.colorTextSecondary};
    font-size: 12px;
  `,

  mappingType: css`
    color: ${token.colorTextTertiary};
    font-size: 11px;
  `,

  mappingActions: css`
    display: flex;
    gap: 4px;
    margin-left: auto;
  `
}))
