images:
	for IMAGE in $$(find static_assets/images -name '*.pbm'); do \
		JPG_IMAGE=$${IMAGE%.pbm}.jpg; \
		make $$JPG_IMAGE; \
	done

${JPG_IMAGE}: ${IMAGE}
		convert ${IMAGE} ${JPG_IMAGE}