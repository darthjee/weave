images:
	for IMAGE in $$(find source/static/assets/images/ -name '*.pbm'); do \
		JPG_IMAGE=$${IMAGE%.pbm}.jpg; \
		make IMAGE=$$IMAGE JPG_IMAGE=$$JPG_IMAGE $$JPG_IMAGE; \
	done

${JPG_IMAGE}: ${IMAGE}
	echo "Creating JPG image from PBM: ${IMAGE} -> ${JPG_IMAGE}"
	convert ${IMAGE} ${JPG_IMAGE}