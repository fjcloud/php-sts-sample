# php-sts-sample

STS trust policy :

```shell
cat <<EOF > ./trust-policy.json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Principal": {
                "Federated": "arn:aws:iam::$(aws sts get-caller-identity --query 'Account' --output text):oidc-provider/$(rosa describe cluster -c ${CLUSTER_NAME} -o json | jq -r .aws.sts.oidc_endpoint_url | sed -e 's/^https:\/\///')" 
            },
            "Action": "sts:AssumeRoleWithWebIdentity",
            "Condition": {
                "StringEquals": {
                    "$(rosa describe cluster -c ${CLUSTER_NAME} -o json | jq -r .aws.sts.oidc_endpoint_url | sed -e 's/^https:\/\///'):sub": "system:serviceaccount:php-sts-sample:default" 
                }
            }
        }
    ]
}
EOF
```

Policy access rds with `iamuser` :

```shell
cat <<EOF > ./rds-connect-policy.json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": [
                "rds-db:connect"
            ],
            "Resource": [
                "arn:aws:rds-db:eu-west-1:660250927410:dbuser:db-PSJPTPUSVHR3THGFRRAZ6LCSJ4/iamuser"
            ]
        }
    ]
}
EOF
```

Create role :

```shell
aws iam create-role --role-name rds_data_access --assume-role-policy-document file://trust-policy.json --description "Role for accesing data on RDS"
```

Attach Policy :

```shell
aws iam attach-role-policy --role-name rds_data_access --policy-arn=arn:aws:iam::aws:policy/AmazonRDSDataFullAccess
```


