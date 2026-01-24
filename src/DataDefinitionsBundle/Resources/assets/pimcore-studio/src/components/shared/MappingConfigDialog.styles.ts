import { createStyles } from 'antd-style'

export const useStyles = createStyles(({ css, token }) => ({
  tabContent: css`
    padding: 16px 0;
  `,
  configSection: css`
    background: ${token.colorFillQuaternary};
    border-radius: ${token.borderRadiusLG}px;
    padding: 16px;
    margin-top: 16px;
  `,
  configSectionTitle: css`
    font-size: 12px;
    font-weight: 600;
    color: ${token.colorTextSecondary};
    letter-spacing: 0.5px;
    margin-bottom: 16px;
  `
}))
