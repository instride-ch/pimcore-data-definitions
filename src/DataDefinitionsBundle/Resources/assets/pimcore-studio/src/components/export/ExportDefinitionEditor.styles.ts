import { createStyles } from 'antd-style'

export const useStyles = createStyles(({ css, token }) => ({
  container: css`
    display: flex;
    flex-direction: column;
    height: 100%;
  `,
  tabs: css`
    flex: 1;
    display: flex;
    flex-direction: column;
  `,
  tabContent: css`
    padding: ${token.padding}px;
    overflow: auto;
    height: calc(100vh - 200px);
  `,
  form: css`
    max-width: 650px;
  `,
  toolbar: css`
    border-top: 1px solid ${token.colorBorderSecondary};
    padding: ${token.paddingSM}px;
    background: ${token.colorBgLayout};
    display: flex;
    justify-content: flex-end;
    gap: ${token.paddingSM}px;
  `,
  mappingHeader: css`
    margin-bottom: ${token.margin}px;
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
    height: 300px;
  `
}))
