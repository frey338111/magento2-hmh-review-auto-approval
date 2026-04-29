# Hmh_ReviewAutoApproval

Magento 2 module for automatically approving product reviews after submission when the configured validation rules pass.

## Features

- Admin configuration under the HMH tab
- Asynchronous review approval through Magento message queue
- Product review submission hook that publishes the review ID after review ratings are aggregated
- Queue consumer that loads the review and approves it when validation passes
- Strategy-style validator pool for adding approval rules
- Default validator for minimum average review rating
- Approval mode support:
  - all selected rules must pass
  - any selected rule may pass

## Configuration

Admin path:

- `Stores > Configuration > HMH > Review Auto Approval`

Config paths:

- `hmh_review_auto_approval/general/enable`
- `hmh_review_auto_approval/general/rules`
- `hmh_review_auto_approval/general/approve_on`
- `hmh_review_auto_approval/default/minimum_rating`

Defaults:

- `general/enable`: `0`
- `general/rules`: `minimum_rating`
- `general/approve_on`: `all_rules_passed`
- `default/minimum_rating`: `3`

## Approval Rules

Configured rules are read from `general/rules`.

The available options are generated from validator names registered in:

- `Hmh\ReviewAutoApproval\Model\Validator\ValidatorPool`

The default validator is:

- `minimum_rating`: approves reviews whose average rating value is greater than or equal to `default/minimum_rating`

## Rule Match Mode

`general/approve_on` controls how selected rules are evaluated.

Available values:

- `all_rules_passed`: every selected validator must pass
- `any_rule_passed`: at least one selected validator must pass

## Queue

Topic:

- `hmh.review.auto.approval`

Queue:

- `hmh.review.auto.approval.queue`

Consumer:

- `hmh.review.auto.approval.consumer`

Run the consumer:

```bash
bin/magento queue:consumers:start hmh.review.auto.approval.consumer
```

Example with a message limit:

```bash
bin/magento queue:consumers:start hmh.review.auto.approval.consumer --max-messages=100
```

If the MySQL queue table does not contain this queue after enabling the module, run:

```bash
bin/magento setup:upgrade
bin/magento cache:clean config
```

## Extending Validators

Create a validator that implements:

- `Hmh\ReviewAutoApproval\Api\ReviewApprovalValidatorInterface`

Register it in `etc/di.xml` under `ValidatorPool`:

```xml
<type name="Hmh\ReviewAutoApproval\Model\Validator\ValidatorPool">
    <arguments>
        <argument name="validators" xsi:type="array">
            <item name="custom_rule" xsi:type="object">Vendor\Module\Model\Validator\CustomRuleValidator</item>
        </argument>
    </arguments>
</type>
```

The `item` name becomes the config option value shown in `general/rules`.

## Dependencies

- `Hmh_Core`
- `Magento_Review`

## Useful Commands

```bash
bin/magento setup:upgrade
bin/magento cache:clean config
bin/magento queue:consumers:start hmh.review.auto.approval.consumer
```
