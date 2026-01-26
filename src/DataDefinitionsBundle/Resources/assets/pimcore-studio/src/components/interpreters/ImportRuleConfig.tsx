/**
 * Import Rule Interpreter Configuration
 *
 * Opens a modal to configure import rules with conditions and actions.
 * Each rule is evaluated in sequence, and if conditions match, actions are applied.
 */

import React, { useState, useEffect, useRef } from 'react'
import {
  Button,
  Modal,
  List,
  Tabs,
  Form,
  Input,
  Switch,
  Select,
  Card,
  Space,
  Empty,
  Dropdown
} from 'antd'
import {
  PlusOutlined,
  DeleteOutlined,
  ArrowUpOutlined,
  ArrowDownOutlined,
  SettingOutlined,
  SearchOutlined,
  ThunderboltOutlined,
  EditOutlined
} from '@ant-design/icons'
import { useTranslation } from 'react-i18next'
import type { InterpreterConfigProps } from './index'
import { RuleActionConfig, RuleConditionConfig } from '../rules'

interface RuleCondition {
  type: string
  configuration: Record<string, any>
}

interface RuleAction {
  type: string
  configuration: Record<string, any>
}

interface ImportRule {
  id: string
  name: string
  active: boolean
  conditions: RuleCondition[]
  actions: RuleAction[]
}

const generateId = (): string => {
  return `rule_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`
}

export const ImportRuleConfig: React.FC<InterpreterConfigProps> = ({
  config,
  definitionConfig,
  onChange
}) => {
  const { t } = useTranslation()
  const [modalOpen, setModalOpen] = useState(false)
  const [rules, setRules] = useState<ImportRule[]>(config.rules || [])
  const [selectedRuleId, setSelectedRuleId] = useState<string | null>(null)

  const availableConditions = definitionConfig?.import_rules?.conditions || []
  const availableActions = definitionConfig?.import_rules?.actions || []
  const initializedRef = useRef(false)

  // Initialize config with correct structure if it doesn't have rules
  useEffect(() => {
    if (!initializedRef.current && !config.rules) {
      initializedRef.current = true
      onChange({ rules: [] })
    }
  }, [config.rules, onChange])

  const selectedRule = rules.find(r => r.id === selectedRuleId)

  const openModal = () => {
    setRules(config.rules || [])
    setSelectedRuleId(rules.length > 0 ? rules[0]?.id : null)
    setModalOpen(true)
  }

  const closeModal = () => {
    setModalOpen(false)
  }

  const saveAndClose = () => {
    // Only save the rules array, don't spread old config that may have incorrect fields
    onChange({ rules })
    setModalOpen(false)
  }

  const addRule = () => {
    const newRule: ImportRule = {
      id: generateId(),
      name: t('data_definitions.import_rule.new_rule'),
      active: true,
      conditions: [],
      actions: []
    }
    const newRules = [...rules, newRule]
    setRules(newRules)
    setSelectedRuleId(newRule.id)
  }

  const deleteRule = (ruleId: string) => {
    const newRules = rules.filter(r => r.id !== ruleId)
    setRules(newRules)
    if (selectedRuleId === ruleId) {
      setSelectedRuleId(newRules.length > 0 ? newRules[0].id : null)
    }
  }

  const updateRule = (ruleId: string, updates: Partial<ImportRule>) => {
    setRules(rules.map(r => r.id === ruleId ? { ...r, ...updates } : r))
  }

  // Condition handlers
  const addCondition = (ruleId: string, type: string) => {
    const rule = rules.find(r => r.id === ruleId)
    if (!rule) return

    const newCondition: RuleCondition = {
      type,
      configuration: {}
    }
    updateRule(ruleId, { conditions: [...rule.conditions, newCondition] })
  }

  const updateCondition = (ruleId: string, index: number, config: Record<string, any>) => {
    const rule = rules.find(r => r.id === ruleId)
    if (!rule) return

    const newConditions = [...rule.conditions]
    newConditions[index] = { ...newConditions[index], configuration: config }
    updateRule(ruleId, { conditions: newConditions })
  }

  const deleteCondition = (ruleId: string, index: number) => {
    const rule = rules.find(r => r.id === ruleId)
    if (!rule) return

    updateRule(ruleId, { conditions: rule.conditions.filter((_, i) => i !== index) })
  }

  const moveCondition = (ruleId: string, index: number, direction: 'up' | 'down') => {
    const rule = rules.find(r => r.id === ruleId)
    if (!rule) return

    const newIndex = direction === 'up' ? index - 1 : index + 1
    if (newIndex < 0 || newIndex >= rule.conditions.length) return

    const newConditions = [...rule.conditions]
    const [removed] = newConditions.splice(index, 1)
    newConditions.splice(newIndex, 0, removed)
    updateRule(ruleId, { conditions: newConditions })
  }

  // Action handlers
  const addAction = (ruleId: string, type: string) => {
    const rule = rules.find(r => r.id === ruleId)
    if (!rule) return

    const newAction: RuleAction = {
      type,
      configuration: {}
    }
    updateRule(ruleId, { actions: [...rule.actions, newAction] })
  }

  const updateAction = (ruleId: string, index: number, config: Record<string, any>) => {
    const rule = rules.find(r => r.id === ruleId)
    if (!rule) return

    const newActions = [...rule.actions]
    newActions[index] = { ...newActions[index], configuration: config }
    updateRule(ruleId, { actions: newActions })
  }

  const deleteAction = (ruleId: string, index: number) => {
    const rule = rules.find(r => r.id === ruleId)
    if (!rule) return

    updateRule(ruleId, { actions: rule.actions.filter((_, i) => i !== index) })
  }

  const moveAction = (ruleId: string, index: number, direction: 'up' | 'down') => {
    const rule = rules.find(r => r.id === ruleId)
    if (!rule) return

    const newIndex = direction === 'up' ? index - 1 : index + 1
    if (newIndex < 0 || newIndex >= rule.actions.length) return

    const newActions = [...rule.actions]
    const [removed] = newActions.splice(index, 1)
    newActions.splice(newIndex, 0, removed)
    updateRule(ruleId, { actions: newActions })
  }

  const renderConditionDropdown = (ruleId: string) => {
    const items = availableConditions.map(type => ({
      key: type,
      label: type,
      onClick: () => addCondition(ruleId, type)
    }))

    return (
      <Dropdown menu={{ items }} trigger={['click']}>
        <Button type="primary" icon={<PlusOutlined />}>
          {t('data_definitions.import_rule.add_condition')}
        </Button>
      </Dropdown>
    )
  }

  const renderActionDropdown = (ruleId: string) => {
    const items = availableActions.map(type => ({
      key: type,
      label: type,
      onClick: () => addAction(ruleId, type)
    }))

    return (
      <Dropdown menu={{ items }} trigger={['click']}>
        <Button type="primary" icon={<PlusOutlined />}>
          {t('data_definitions.import_rule.add_action')}
        </Button>
      </Dropdown>
    )
  }

  const renderRuleDetail = (rule: ImportRule) => {
    const tabItems = [
      {
        key: 'settings',
        label: (
          <span>
            <SettingOutlined /> {t('data_definitions.settings')}
          </span>
        ),
        children: (
          <Form layout="vertical" style={{ padding: 16 }}>
            <Form.Item label={t('data_definitions.name')}>
              <Input
                value={rule.name}
                onChange={e => updateRule(rule.id, { name: e.target.value })}
              />
            </Form.Item>
            <Form.Item label={t('data_definitions.active')}>
              <Switch
                checked={rule.active}
                onChange={checked => updateRule(rule.id, { active: checked })}
              />
            </Form.Item>
          </Form>
        )
      },
      {
        key: 'conditions',
        label: (
          <span>
            <SearchOutlined /> {t('data_definitions.import_rule.conditions')}
          </span>
        ),
        children: (
          <div style={{ padding: 16 }}>
            <div style={{ marginBottom: 16 }}>
              {renderConditionDropdown(rule.id)}
            </div>
            {rule.conditions.length === 0 ? (
              <Empty
                description={t('data_definitions.import_rule.no_conditions')}
                image={Empty.PRESENTED_IMAGE_SIMPLE}
              />
            ) : (
              <Space direction="vertical" style={{ width: '100%' }}>
                {rule.conditions.map((condition, index) => (
                  <Card
                    key={index}
                    size="small"
                    title={
                      <Space>
                        <SettingOutlined />
                        {condition.type}
                      </Space>
                    }
                    extra={
                      <Space>
                        <Button
                          type="text"
                          size="small"
                          icon={<ArrowUpOutlined />}
                          onClick={() => moveCondition(rule.id, index, 'up')}
                          disabled={index === 0}
                        />
                        <Button
                          type="text"
                          size="small"
                          icon={<ArrowDownOutlined />}
                          onClick={() => moveCondition(rule.id, index, 'down')}
                          disabled={index === rule.conditions.length - 1}
                        />
                        <Button
                          type="text"
                          size="small"
                          danger
                          icon={<DeleteOutlined />}
                          onClick={() => deleteCondition(rule.id, index)}
                        />
                      </Space>
                    }
                  >
                    <RuleConditionConfig
                      type={condition.type}
                      config={condition.configuration}
                      onChange={cfg => updateCondition(rule.id, index, cfg)}
                    />
                  </Card>
                ))}
              </Space>
            )}
          </div>
        )
      },
      {
        key: 'actions',
        label: (
          <span>
            <ThunderboltOutlined /> {t('data_definitions.import_rule.actions')}
          </span>
        ),
        children: (
          <div style={{ padding: 16 }}>
            <div style={{ marginBottom: 16 }}>
              {renderActionDropdown(rule.id)}
            </div>
            {rule.actions.length === 0 ? (
              <Empty
                description={t('data_definitions.import_rule.no_actions')}
                image={Empty.PRESENTED_IMAGE_SIMPLE}
              />
            ) : (
              <Space direction="vertical" style={{ width: '100%' }}>
                {rule.actions.map((action, index) => (
                  <Card
                    key={index}
                    size="small"
                    title={
                      <Space>
                        <SettingOutlined />
                        {action.type}
                      </Space>
                    }
                    extra={
                      <Space>
                        <Button
                          type="text"
                          size="small"
                          icon={<ArrowUpOutlined />}
                          onClick={() => moveAction(rule.id, index, 'up')}
                          disabled={index === 0}
                        />
                        <Button
                          type="text"
                          size="small"
                          icon={<ArrowDownOutlined />}
                          onClick={() => moveAction(rule.id, index, 'down')}
                          disabled={index === rule.actions.length - 1}
                        />
                        <Button
                          type="text"
                          size="small"
                          danger
                          icon={<DeleteOutlined />}
                          onClick={() => deleteAction(rule.id, index)}
                        />
                      </Space>
                    }
                  >
                    <RuleActionConfig
                      type={action.type}
                      config={action.configuration}
                      onChange={cfg => updateAction(rule.id, index, cfg)}
                    />
                  </Card>
                ))}
              </Space>
            )}
          </div>
        )
      }
    ]

    return (
      <Tabs defaultActiveKey="settings" items={tabItems} />
    )
  }

  return (
    <>
      <Button icon={<EditOutlined />} onClick={openModal}>
        {t('data_definitions.edit')}
      </Button>

      <Modal
        title={t('data_definitions.import_rules')}
        open={modalOpen}
        onCancel={closeModal}
        onOk={saveAndClose}
        width="80%"
        style={{ top: 20 }}
        styles={{ body: { height: 'calc(80vh - 110px)', padding: 0 } }}
        okText={t('data_definitions.save')}
        cancelText={t('data_definitions.cancel')}
      >
        <div style={{ display: 'flex', height: '100%' }}>
          {/* Left panel - Rule list */}
          <div style={{
            width: 250,
            borderRight: '1px solid #f0f0f0',
            display: 'flex',
            flexDirection: 'column'
          }}>
            <div style={{ padding: 8, borderBottom: '1px solid #f0f0f0' }}>
              <Button
                type="primary"
                icon={<PlusOutlined />}
                onClick={addRule}
                block
              >
                {t('data_definitions.add')}
              </Button>
            </div>
            <div style={{ flex: 1, overflow: 'auto' }}>
              <List
                dataSource={rules}
                renderItem={rule => (
                  <List.Item
                    key={rule.id}
                    onClick={() => setSelectedRuleId(rule.id)}
                    style={{
                      padding: '8px 12px',
                      cursor: 'pointer',
                      background: selectedRuleId === rule.id ? '#e6f7ff' : 'transparent',
                      borderLeft: selectedRuleId === rule.id ? '3px solid #1890ff' : '3px solid transparent'
                    }}
                    actions={[
                      <Button
                        key="delete"
                        type="text"
                        size="small"
                        danger
                        icon={<DeleteOutlined />}
                        onClick={e => {
                          e.stopPropagation()
                          deleteRule(rule.id)
                        }}
                      />
                    ]}
                  >
                    <span style={{ opacity: rule.active ? 1 : 0.5 }}>
                      {rule.name}
                    </span>
                  </List.Item>
                )}
                locale={{ emptyText: t('data_definitions.import_rule.no_rules') }}
              />
            </div>
          </div>

          {/* Right panel - Rule detail */}
          <div style={{ flex: 1, overflow: 'auto' }}>
            {selectedRule ? (
              renderRuleDetail(selectedRule)
            ) : (
              <Empty
                description={t('data_definitions.import_rule.select_or_create')}
                style={{ marginTop: 100 }}
              />
            )}
          </div>
        </div>
      </Modal>
    </>
  )
}
