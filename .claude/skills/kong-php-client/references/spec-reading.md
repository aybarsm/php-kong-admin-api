# Reading the Kong spec

The canonical file is `resources/kong-admin-api/v3.16.json` (JSON is easier to query than YAML).
`S=resources/kong-admin-api/v3.16.json`

## Existence check (always first)
`test -s "$S" || { echo "Missing spec: $S"; exit 1; }`

## All operations for an entity (method, path, operationId), excluding workspace twins
jq -r '.paths|to_entries[]|select(.key|test("^/services"))|.key as $p|.value|to_entries[]
  |select(.key|IN("get","post","put","patch","delete","head","options"))
  |"\(.key|ascii_upcase)\t\($p)\t\(.value.operationId)"' "$S"

## Does a workspace twin exist?
jq -e --arg p "/{workspace}/services/{ServiceIdOrName}" '.paths[$p]' "$S" >/dev/null && echo twin

## Parameters of one operation (path-level and operation-level, $refs resolved)
jq --arg p "/services" --arg m get '
  ((.paths[$p].parameters//[]) + (.paths[$p][$m].parameters//[]))
  | map(if ."$ref" then (."$ref"|split("/")|last) as $n | $ROOT.components.parameters[$n] else . end)' \
  --argjson ROOT "$(cat "$S")" "$S"
(Simpler: list the `$ref` names, then `jq '.components.parameters.PaginationSize' "$S"`.)

## Request and response schema of an operation
jq '.paths["/services"].post.requestBody.content["application/json"].schema' "$S"
jq '.paths["/services"].get.responses' "$S"

## A component schema, summarised
jq '.components.schemas.Service | {required, props: (.properties|map_values({type, nullable,
  enum, default, writeOnly, readOnly, fk: ."x-foreign", secret: ."x-encrypted", ref: ."$ref"}))}' "$S"

## Every enum location
jq -r '[paths(type=="object" and has("enum"))] | .[] | map(tostring) | join(".")' "$S"

## Inventory for version diffs (run on both specs, then `diff`)
jq -r '.paths|to_entries[]|.key as $p|.value|to_entries[]|select(.value|type=="object")
  |select(.key|IN("get","post","put","patch","delete","head","options"))
  |"\(.key|ascii_upcase) \($p) \(.value.operationId)"' "$S" | sort > /tmp/ops-old.txt
jq -S '.components.schemas' "$S" > /tmp/schemas-old.json   # then: diff <(jq -S … new) …
jq -S '.components.parameters' "$S" > /tmp/params-old.json

## Gotchas observed in v3.16
- `PaginationOffset` is a string. The integer `pagination-offset` parameter is defined but never used.
- `oneOf` without a discriminator (Route), and with one (Partial, on `type`).
- `*WithoutParents` schemas are used for nested POST and PUT. PATCH uses the full schema.
- 404 responses have no body. Delete returns 204 even when the resource is missing.
- `{workspace}` twins have identical schemas. Workspace-only ops live under `/{workspace}/rbac/`.
- Path parameter descriptions can be copy-pasted wrongly (`UpstreamIdForTarget`). Trust the name,
  and record the discrepancy in docs/spec-notes.md.
- YAML uses scientific notation for large ints, so query the JSON.
