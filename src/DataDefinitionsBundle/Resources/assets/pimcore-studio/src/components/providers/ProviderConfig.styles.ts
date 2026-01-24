/**
 * Provider Configuration Styles
 */

import { createStyles } from 'antd-style'

export const useProviderStyles = createStyles(({ token, css }) => ({
  container: css`
    padding: 16px;
  `,

  section: css`
    background: ${token.colorBgContainer};
    border: 1px solid ${token.colorBorderSecondary};
    border-radius: ${token.borderRadiusLG}px;
    padding: 16px;
    margin-bottom: 16px;

    &:last-child {
      margin-bottom: 0;
    }
  `,

  sectionTitle: css`
    font-size: 13px;
    font-weight: 600;
    color: ${token.colorTextSecondary};
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 16px;
    padding-bottom: 8px;
    border-bottom: 1px solid ${token.colorBorderSecondary};
    display: flex;
    align-items: center;
    gap: 8px;
  `,

  sectionIcon: css`
    font-size: 16px;
    color: ${token.colorPrimary};
  `,

  row: css`
    display: flex;
    gap: 16px;
    margin-bottom: 16px;

    &:last-child {
      margin-bottom: 0;
    }
  `,

  field: css`
    flex: 1;
  `,

  fieldSmall: css`
    width: 120px;
    flex-shrink: 0;
  `,

  fieldLabel: css`
    font-size: 12px;
    font-weight: 500;
    color: ${token.colorText};
    margin-bottom: 6px;
    display: block;
  `,

  fieldHelp: css`
    font-size: 11px;
    color: ${token.colorTextTertiary};
    margin-top: 4px;
  `,

  codeEditor: css`
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
    font-size: 12px;
    background: ${token.colorFillTertiary};
    border-radius: ${token.borderRadius}px;

    &.ant-input {
      background: ${token.colorFillTertiary};
    }
  `,

  previewSection: css`
    background: ${token.colorFillAlter};
    border: 1px dashed ${token.colorBorder};
    border-radius: ${token.borderRadius}px;
    padding: 12px;
    margin-top: 8px;
  `,

  previewLabel: css`
    font-size: 11px;
    font-weight: 500;
    color: ${token.colorTextSecondary};
    margin-bottom: 8px;
    text-transform: uppercase;
  `,

  previewContent: css`
    font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
    font-size: 11px;
    color: ${token.colorText};
    white-space: pre-wrap;
    word-break: break-all;
  `,

  inlineGroup: css`
    display: flex;
    gap: 12px;
    align-items: flex-start;
  `,

  checkbox: css`
    margin-top: 4px;
  `,

  divider: css`
    height: 1px;
    background: ${token.colorBorderSecondary};
    margin: 16px 0;
  `,

  alert: css`
    margin-bottom: 16px;
  `,

  storageSelect: css`
    min-width: 200px;
  `
}))
