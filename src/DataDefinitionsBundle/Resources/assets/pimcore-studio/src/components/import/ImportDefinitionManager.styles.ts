import { createStyles } from 'antd-style'

export const useStyles = createStyles(({ css, token }) => ({
  container: css`
    display: flex;
    flex-direction: column;
    height: 100%;
    overflow: hidden;
  `,
  listPanel: css`
    height: 100%;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: ${token.colorBgContainer};
  `,
  listHeader: css`
    padding: 12px 16px;
    background: ${token.colorBgContainer};
    border-bottom: 1px solid ${token.colorBorderSecondary};
    display: flex;
    align-items: center;
    justify-content: space-between;
  `,
  listTitle: css`
    margin: 0 !important;
    font-size: 12px;
    font-weight: 600;
    color: ${token.colorTextSecondary};
    text-transform: uppercase;
    letter-spacing: 0.5px;
  `,
  listBody: css`
    flex: 1 1 auto;
    min-height: 0;
    overflow: auto;
    padding: 8px;
  `,
  listFooter: css`
    padding: 8px 16px;
    border-top: 1px solid ${token.colorBorderSecondary};
    font-size: ${token.fontSizeSM}px;
    color: ${token.colorTextSecondary};
    background: ${token.colorBgContainer};
  `,
  editorPanel: css`
    height: 100%;
    min-height: 0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    background: ${token.colorBgElevated};
  `,
  emptyState: css`
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    color: ${token.colorTextSecondary};
    background: ${token.colorBgElevated};
  `,
  loadingContainer: css`
    padding: ${token.paddingLG}px;
    text-align: center;
  `
}))
