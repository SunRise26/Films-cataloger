TODO:
1. Handle mysql "SELECT" limits, currently used defaults. (4096 columns per table). For example, use page listing for films list.
2. Check for vulnerabilities. I'm completely sure that missed this part in a hurry. (e.g. maybe somewhere missed sql injections)
3. Move part of functions from filmModel to some kind of "helpers";
4. Improve searching (e.g. generating search map, using search libraries ...)
