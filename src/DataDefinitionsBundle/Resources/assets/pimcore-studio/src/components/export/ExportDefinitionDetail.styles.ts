import { createStyles } from 'antd-style'

export const useStyles = createStyles(({ css, token }) => ({
  root: css`
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;

    .ant-tabs {
      flex: 1;
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }

    .ant-tabs-content-holder {
      flex: 1;
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
  header: css`
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 24px;
    border-bottom: 1px solid ${token.colorBorderSecondary};
    background: ${token.colorBgContainer};
    flex-shrink: 0;
  `,
  title: css`
    font-size: 16px;
    font-weight: 600;
    color: ${token.colorText};
  `,
  tabContent: css`
    padding: 24px;
    height: 100%;
    overflow: auto;
  `,
  emptyState: css`
    color: ${token.colorTextSecondary};
    text-align: center;
    padding: 48px;
  `
}))
