import { createStyles } from 'antd-style'

export const useStyles = createStyles(({ css, token }) => ({
  container: css`
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
  `,
  header: css`
    padding: 12px 24px;
    background: ${token.colorBgContainer};
    border-bottom: 1px solid ${token.colorBorderSecondary};
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
  `,
  headerTitle: css`
    margin: 0 !important;
    font-size: 14px;
    font-weight: 600;
  `,
  tabs: css`
    flex: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;

    .ant-tabs-nav {
      padding: 0 24px;
      margin-bottom: 0;
      background: ${token.colorBgContainer};
    }

    .ant-tabs-content-holder {
      flex: 1;
      min-height: 0;
      overflow: hidden;
    }

    .ant-tabs-content {
      height: 100%;
    }

    .ant-tabs-tabpane {
      height: 100%;
      overflow: auto;
    }
  `,
  tabContent: css`
    padding: 24px;
    height: 100%;
    overflow: auto;
  `,
  form: css`
    max-width: 800px;
  `,
  toolbar: css`
    border-top: 1px solid ${token.colorBorderSecondary};
    padding: 12px 24px;
    background: ${token.colorBgContainer};
    display: flex;
    justify-content: flex-end;
    gap: ${token.marginSM}px;
    flex-shrink: 0;
  `,
  mappingHeader: css`
    display: flex;
    background: ${token.colorBgLayout};
    border-bottom: 1px solid ${token.colorBorderSecondary};
    padding: ${token.paddingSM}px ${token.paddingSM}px ${token.paddingSM}px 48px;
    font-weight: 600;
    font-size: ${token.fontSizeSM}px;
    color: ${token.colorTextSecondary};
  `,
  mappingHeaderCol: css`
    width: 120px;
  `,
  mappingHeaderColFrom: css`
    width: 150px;
    margin-left: ${token.marginSM}px;
  `,
  mappingHeaderColPrimary: css`
    margin-left: ${token.marginSM}px;
  `,
  mappingRow: css`
    display: inline-flex;
    align-items: center;
    gap: ${token.marginSM}px;
    min-width: 600px;
  `,
  mappingRowToColumn: css`
    width: 120px;
    font-weight: 500;
    overflow: hidden;
    text-overflow: ellipsis;
  `,
  mappingRowFromColumn: css`
    width: 150px;
  `,
  emptyProvider: css`
    color: ${token.colorTextSecondary};
    padding: ${token.paddingXL}px;
    text-align: center;
  `,
  loadingContainer: css`
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
    min-height: 300px;
  `,
  groupTitle: css`
    font-weight: 500;
  `
}))
